# ai_service.py - AI Service untuk Generate Konten
import os
import json
from dotenv import load_dotenv

load_dotenv()

# ============================================
# OpenAI Service
# ============================================
try:
    import openai
    openai.api_key = os.getenv('OPENAI_API_KEY')
    OPENAI_AVAILABLE = True if openai.api_key else False
except:
    OPENAI_AVAILABLE = False

# ============================================
# Google Gemini Service
# ============================================
try:
    import google.generativeai as genai
    genai.configure(api_key=os.getenv('GEMINI_API_KEY'))
    GEMINI_AVAILABLE = True if os.getenv('GEMINI_API_KEY') else False
except:
    GEMINI_AVAILABLE = False

# ============================================
# Generate Konten Perangkat Ajar
# ============================================
def generate_perangkat_ajar(jenis, topik, kelas, mapel):
    """
    Generate konten perangkat ajar menggunakan AI
    """
    prompt = f"""
    Buatlah {jenis} untuk mata pelajaran {mapel} kelas {kelas} dengan topik "{topik}".
    
    Silakan buat dengan struktur:
    1. Judul: {jenis} {mapel} Kelas {kelas} - {topik}
    2. Deskripsi: Deskripsi singkat tentang materi
    3. Tujuan Pembelajaran: 4-5 tujuan pembelajaran
    4. Langkah Pembelajaran: Pendahuluan, Kegiatan Inti, Penutup
    5. Penilaian: Jenis penilaian yang digunakan
    
    Buat dalam format JSON dengan key: judul, deskripsi, tujuan, langkah, penilaian
    """
    
    result = {
        'judul': f"{jenis.upper()} {mapel} Kelas {kelas} - {topik}",
        'deskripsi': f"Dokumen {jenis} ini membahas tentang {topik} untuk kelas {kelas} mata pelajaran {mapel}. Disusun untuk memenuhi kebutuhan pembelajaran yang efektif dan menyenangkan.",
        'tujuan': "1. Peserta didik dapat memahami konsep {topik}\n2. Peserta didik dapat menerapkan {topik} dalam kehidupan sehari-hari\n3. Peserta didik dapat menganalisis {topik} secara kritis\n4. Peserta didik dapat mengevaluasi {topik} dengan tepat",
        'langkah': "1. Pendahuluan (10 menit)\n   - Guru membuka pembelajaran dengan salam dan doa\n   - Guru menyampaikan tujuan pembelajaran\n   - Guru melakukan apersepsi terkait {topik}\n\n2. Kegiatan Inti (60 menit)\n   - Guru menjelaskan konsep {topik} secara interaktif\n   - Peserta didik berdiskusi dalam kelompok\n   - Peserta didik mempresentasikan hasil diskusi\n   - Guru memberikan penguatan materi\n\n3. Penutup (20 menit)\n   - Guru dan peserta didik menyimpulkan pembelajaran\n   - Guru memberikan refleksi dan tindak lanjut\n   - Guru menutup pembelajaran dengan salam",
        'penilaian': "1. Penilaian Sikap: Observasi selama pembelajaran\n2. Penilaian Pengetahuan: Tes tertulis (10 soal pilihan ganda)\n3. Penilaian Keterampilan: Presentasi kelompok\n4. Penilaian Produk: Laporan hasil diskusi"
    }
    
    # Gunakan OpenAI jika tersedia
    if OPENAI_AVAILABLE:
        try:
            response = openai.ChatCompletion.create(
                model="gpt-3.5-turbo",
                messages=[
                    {"role": "system", "content": "Anda adalah asisten guru yang ahli dalam membuat perangkat pembelajaran. Berikan respons dalam format JSON."},
                    {"role": "user", "content": prompt}
                ],
                temperature=0.7,
                max_tokens=1500
            )
            
            content = response.choices[0].message.content
            # Ekstrak JSON dari response
            try:
                # Cari JSON dalam response
                start = content.find('{')
                end = content.rfind('}') + 1
                if start != -1 and end != -1:
                    json_str = content[start:end]
                    ai_result = json.loads(json_str)
                    if all(key in ai_result for key in ['judul', 'deskripsi', 'tujuan', 'langkah', 'penilaian']):
                        return ai_result
            except:
                pass
        except Exception as e:
            print(f"OpenAI Error: {e}")
    
    # Gunakan Gemini jika tersedia
    if GEMINI_AVAILABLE:
        try:
            model = genai.GenerativeModel('gemini-pro')
            response = model.generate_content(prompt + "\n\nBerikan dalam format JSON.")
            content = response.text
            try:
                start = content.find('{')
                end = content.rfind('}') + 1
                if start != -1 and end != -1:
                    json_str = content[start:end]
                    ai_result = json.loads(json_str)
                    if all(key in ai_result for key in ['judul', 'deskripsi', 'tujuan', 'langkah', 'penilaian']):
                        return ai_result
            except:
                pass
        except Exception as e:
            print(f"Gemini Error: {e}")
    
    # Fallback: Template statis dengan format yang benar
    return {
        'judul': f"{jenis.upper()} {mapel} Kelas {kelas} - {topik}",
        'deskripsi': f"Dokumen {jenis} ini membahas tentang {topik} untuk kelas {kelas} mata pelajaran {mapel}. Disusun untuk memenuhi kebutuhan pembelajaran yang efektif dan menyenangkan.",
        'tujuan': f"1. Peserta didik dapat memahami konsep {topik}\n2. Peserta didik dapat menerapkan {topik} dalam kehidupan sehari-hari\n3. Peserta didik dapat menganalisis {topik} secara kritis\n4. Peserta didik dapat mengevaluasi {topik} dengan tepat",
        'langkah': f"1. Pendahuluan (10 menit)\n   - Guru membuka pembelajaran dengan salam dan doa\n   - Guru menyampaikan tujuan pembelajaran\n   - Guru melakukan apersepsi terkait {topik}\n\n2. Kegiatan Inti (60 menit)\n   - Guru menjelaskan konsep {topik} secara interaktif\n   - Peserta didik berdiskusi dalam kelompok\n   - Peserta didik mempresentasikan hasil diskusi\n   - Guru memberikan penguatan materi\n\n3. Penutup (20 menit)\n   - Guru dan peserta didik menyimpulkan pembelajaran\n   - Guru memberikan refleksi dan tindak lanjut\n   - Guru menutup pembelajaran dengan salam",
        'penilaian': "1. Penilaian Sikap: Observasi selama pembelajaran\n2. Penilaian Pengetahuan: Tes tertulis (10 soal pilihan ganda)\n3. Penilaian Keterampilan: Presentasi kelompok\n4. Penilaian Produk: Laporan hasil diskusi"
    }


# ============================================
# Generate Rekomendasi Pembelajaran
# ============================================
def generate_rekomendasi(mapel, kelas, topik):
    """
    Generate rekomendasi metode pembelajaran
    """
    prompt = f"""
    Berikan rekomendasi metode pembelajaran yang efektif untuk:
    - Mata Pelajaran: {mapel}
    - Kelas: {kelas}
    - Topik: {topik}
    
    Berikan dalam format JSON dengan key: metode, media, strategi, evaluasi
    """
    
    # Fallback template
    result = {
        'metode': f"1. Metode Ceramah Interaktif\n2. Metode Diskusi Kelompok\n3. Metode Discovery Learning",
        'media': f"1. Slide Presentasi\n2. Video Pembelajaran\n3. Modul Praktikum",
        'strategi': f"1. Pendekatan Saintifik\n2. Pembelajaran Berbasis Masalah\n3. Pembelajaran Kooperatif",
        'evaluasi': f"1. Tes Formatif\n2. Tes Sumatif\n3. Penilaian Portofolio"
    }
    
    # Gunakan AI jika tersedia
    if OPENAI_AVAILABLE:
        try:
            response = openai.ChatCompletion.create(
                model="gpt-3.5-turbo",
                messages=[
                    {"role": "system", "content": "Anda adalah ahli pedagogi. Berikan rekomendasi dalam format JSON."},
                    {"role": "user", "content": prompt}
                ],
                temperature=0.7,
                max_tokens=500
            )
            content = response.choices[0].message.content
            try:
                start = content.find('{')
                end = content.rfind('}') + 1
                if start != -1 and end != -1:
                    json_str = content[start:end]
                    ai_result = json.loads(json_str)
                    if all(key in ai_result for key in ['metode', 'media', 'strategi', 'evaluasi']):
                        return ai_result
            except:
                pass
        except:
            pass
    
    return result