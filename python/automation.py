# automation.py - Otomatisasi Tugas
import schedule
import time
import mysql.connector
from datetime import datetime, timedelta
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import os
from dotenv import load_dotenv

load_dotenv()

# ============================================
# KONEKSI DATABASE
# ============================================
def get_db_connection():
    return mysql.connector.connect(
        host=os.getenv('DB_HOST', 'localhost'),
        user=os.getenv('DB_USER', 'root'),
        password=os.getenv('DB_PASSWORD', ''),
        database=os.getenv('DB_NAME', 'perangkat_pembelajaran')
    )

# ============================================
# NOTIFIKASI EMAIL
# ============================================
def send_email(to_email, subject, body):
    """Kirim email notifikasi"""
    try:
        sender_email = os.getenv('EMAIL_SENDER', '')
        sender_password = os.getenv('EMAIL_PASSWORD', '')
        smtp_server = os.getenv('SMTP_SERVER', 'smtp.gmail.com')
        smtp_port = int(os.getenv('SMTP_PORT', 587))
        
        if not sender_email or not sender_password:
            print("Email not configured")
            return False
        
        msg = MIMEMultipart()
        msg['From'] = sender_email
        msg['To'] = to_email
        msg['Subject'] = subject
        
        msg.attach(MIMEText(body, 'html'))
        
        server = smtplib.SMTP(smtp_server, smtp_port)
        server.starttls()
        server.login(sender_email, sender_password)
        server.send_message(msg)
        server.quit()
        return True
    except Exception as e:
        print(f"Email error: {e}")
        return False

# ============================================
# NOTIFIKASI DOKUMEN PENDING
# ============================================
def notifikasi_dokumen_pending():
    """Kirim notifikasi untuk dokumen yang sudah lama pending"""
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    
    # Cari dokumen pending > 7 hari
    query = """
        SELECT d.*, u.name as guru_name, u.email as guru_email
        FROM dokumen_perangkat d
        JOIN users u ON d.id_guru = u.id
        WHERE d.status IN ('pending_kepsek', 'pending_pengawas')
          AND d.created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
          AND d.updated_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
    """
    cursor.execute(query)
    pending_docs = cursor.fetchall()
    
    for doc in pending_docs:
        # Notifikasi ke Kepala Sekolah
        # (Implementasi sesuai kebutuhan)
        print(f"Reminder: Dokumen {doc['judul']} masih pending selama 7+ hari")
    
    conn.close()
    return len(pending_docs)

# ============================================
# ARSIPKAN DOKUMEN OTOMATIS
# ============================================
def arsipkan_dokumen_otomatis():
    """Arsipkan dokumen yang sudah terverifikasi > 1 tahun"""
    conn = get_db_connection()
    cursor = conn.cursor()
    
    query = """
        UPDATE dokumen_perangkat 
        SET status = 'arsip' 
        WHERE status = 'terverifikasi' 
          AND created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)
    """
    cursor.execute(query)
    affected = cursor.rowcount
    conn.commit()
    conn.close()
    
    return affected

# ============================================
# GENERATE LAPORAN OTOMATIS
# ============================================
def generate_laporan_periode(periode='bulanan'):
    """Generate laporan otomatis"""
    from data_analyzer import generate_laporan_excel
    
    output = generate_laporan_excel()
    
    # Simpan laporan
    filename = f"laporan_{periode}_{datetime.now().strftime('%Y%m%d')}.xlsx"
    filepath = os.path.join('reports', filename)
    
    os.makedirs('reports', exist_ok=True)
    with open(filepath, 'wb') as f:
        f.write(output.getvalue())
    
    return filepath

# ============================================
# SCHEDULER
# ============================================
def run_scheduler():
    """Jalankan scheduler"""
    # Jadwalkan tugas
    schedule.every().day.at("08:00").do(notifikasi_dokumen_pending)
    schedule.every().month.at("01:00").do(arsipkan_dokumen_otomatis)
    schedule.every().month.at("02:00").do(generate_laporan_periode, 'bulanan')
    
    print("Scheduler started. Running tasks...")
    
    while True:
        schedule.run_pending()
        time.sleep(60)

if __name__ == "__main__":
    run_scheduler()