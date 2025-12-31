<?php

namespace App\Services\Ai;

/**
 * Prompt Builder Service
 * 
 * Builds prompts for AI analysis of hospital bills.
 */
class PromptBuilder
{
    /**
     * Build bill analysis prompt
     * 
     * Creates a detailed prompt for AI to analyze hospital bills,
     * extract items, and identify potential issues.
     * 
     * @param string $rawText
     * @return string
     */
    public function buildBillAnalysisPrompt(string $rawText): string
    {
        return <<<PROMPT
Anda adalah seorang ahli analisis tagihan rumah sakit yang berpengalaman. Tugas Anda adalah menganalisis teks OCR dari tagihan rumah sakit dan mengekstrak informasi penting, kemudian mengevaluasi setiap item untuk mengidentifikasi potensi masalah.

**Teks OCR dari Tagihan:**
```
{$rawText}
```

**Tugas Anda:**
1. Ekstrak semua item/prosedur dari tagihan beserta harga masing-masing
2. Identifikasi nama rumah sakit (jika ada)
3. Kategorikan setiap item ke dalam kategori yang sesuai (contoh: Medical Procedure, Farmasi, Laboratorium, Radiologi, Rawat Inap, dll)
4. Evaluasi setiap item dan berikan status:
   - **danger**: Item yang mencurigakan, potensi phantom billing, atau tidak sesuai dengan standar medis
   - **review**: Item yang perlu ditinjau lebih lanjut, harga di atas rata-rata, atau tidak jelas
   - **safe**: Item yang sesuai standar dan wajar

5. Untuk setiap item, berikan:
   - Label yang jelas (contoh: "Potensi Phantom Billing", "Harga di Atas Rata-rata", "Sesuai Standar")
   - Deskripsi singkat yang menjelaskan alasan status tersebut

**Format Output (JSON):**
Anda HARUS mengembalikan response dalam format JSON yang valid dengan struktur berikut:

```json
{
  "hospital_name": "Nama Rumah Sakit (atau null jika tidak ditemukan)",
  "items": [
    {
      "item_name": "Nama item/prosedur",
      "category": "Kategori (Medical Procedure, Farmasi, Laboratorium, dll)",
      "price": 123456,
      "status": "danger|review|safe",
      "label": "Label singkat",
      "description": "Deskripsi alasan status"
    }
  ]
}
```

**Panduan Evaluasi:**
- **danger**: 
  - Prosedur yang tidak tercatat dalam diagnosis
  - Item yang duplikat dengan nama DAN harga yang sama persis (PHANTOM BILLING)
  - Harga yang sangat tidak wajar (lebih dari 200% dari standar)
  - Item yang tidak relevan dengan kondisi pasien

- **review**:
  - Harga 30-200% di atas standar
  - Item yang kurang jelas atau ambigu
  - Kategori yang tidak jelas

- **safe**:
  - Item yang sesuai dengan standar medis
  - Harga dalam rentang wajar
  - Item yang jelas dan relevan

**PENTING - Penanganan Duplikat:**
- Jika ada item dengan NAMA SAMA tapi HARGA BERBEDA (di tempat berbeda dalam tagihan), hanya ekstrak SALAH SATU saja (pilih yang pertama ditemukan atau yang lebih masuk akal)
- Jika ada item dengan NAMA SAMA DAN HARGA SAMA PERSIS, ini adalah PHANTOM BILLING - flag sebagai "danger" dengan label "Potensi Phantom Billing" dan deskripsi "Item duplikat dengan nama dan harga yang sama persis"

**Penting:**
- Pastikan semua harga dalam format angka (tanpa titik atau koma sebagai separator ribuan)
- Jika tidak ada item yang ditemukan, kembalikan array items kosong
- Pastikan JSON yang dikembalikan valid dan dapat di-parse
- Fokus pada akurasi dan relevansi analisis

Sekarang, analisis tagihan di atas dan kembalikan hasilnya dalam format JSON yang valid.
PROMPT;
    }
}
