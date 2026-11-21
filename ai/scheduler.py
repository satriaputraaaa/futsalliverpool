from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector
from datetime import datetime

app = Flask(__name__)
CORS(app)

def get_db_connection():
    return mysql.connector.connect(
        host="localhost", user="root", password="", database="dbfutsal"
    )

@app.route('/detect-gap', methods=['POST', 'OPTIONS'])
def detect_gap():
    if request.method == 'OPTIONS':
        return '', 200
        
    data = request.json
    tgl_target_str = data.get('tanggal')
    
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    try:
        tgl_obj = datetime.strptime(tgl_target_str, "%Y-%m-%d").date()

        # FIX: Menggunakan CAST ke TIME agar pengurutan jam di database dijamin valid secara kronologis
        cursor.execute("""
            SELECT id_booking, nama_tim, no_whatsapp, tgl_main, jam_mulai, jam_selesai 
            FROM booking 
            WHERE tgl_main = %s 
            ORDER BY CAST(jam_mulai AS TIME) ASC
        """, (tgl_obj,))
        bookings = cursor.fetchall()
        
        if len(bookings) < 2:
            return jsonify({"status": "safe"})

        # Periksa celah di antara setiap jadwal yang berurutan
        for i in range(len(bookings) - 1):
            # Membersihkan format jam (jika ada yang pakai titik '.' di database, ubah ke ':')
            str_curr_end = str(bookings[i]['jam_selesai']).replace('.', ':')
            str_next_start = str(bookings[i+1]['jam_mulai']).replace('.', ':')
            
            curr_end = datetime.strptime(str_curr_end, "%H:%M:%S" if len(str_curr_end.split(':'))==3 else "%H:%M")
            next_start = datetime.strptime(str_next_start, "%H:%M:%S" if len(str_next_start.split(':'))==3 else "%H:%M")
            
            # Jika ada celah kosong
            if next_start > curr_end:
                candidate = bookings[i+1]
                str_cand_start = str(candidate['jam_mulai']).replace('.', ':')
                str_cand_end = str(candidate['jam_selesai']).replace('.', ':')
                
                cand_start = datetime.strptime(str_cand_start, "%H:%M:%S" if len(str_cand_start.split(':'))==3 else "%H:%M")
                cand_end = datetime.strptime(str_cand_end, "%H:%M:%S" if len(str_cand_end.split(':'))==3 else "%H:%M")
                
                durasi = cand_end - cand_start
                target_start = curr_end
                
                if target_start < cand_start:
                    raw_wa = str(candidate['no_whatsapp'])
                    clean_wa = "".join(filter(str.isdigit, raw_wa))
                    
                    if clean_wa.startswith('0'):
                        clean_wa = '62' + clean_wa[1:]
                        
                    return jsonify({
                        "status": "proactive",
                        "id_booking": candidate['id_booking'],
                        "nama_tim": candidate['nama_tim'],
                        "no_wa": clean_wa,
                        "jam_lama": cand_start.strftime("%H:%M"),
                        "jam_baru": target_start.strftime("%H:%M")
                    })
        
        return jsonify({"status": "safe"})
                        
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500
    finally:
        conn.close()

if __name__ == '__main__':
    app.run(port=5000, debug=True)