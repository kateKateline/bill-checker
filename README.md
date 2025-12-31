# 🏥 BillCheck - Hospital Bill Transparency Analyzer

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**BillCheck** adalah aplikasi web yang membantu menganalisis transparansi tagihan rumah sakit menggunakan teknologi OCR (Optical Character Recognition) dan AI untuk mengidentifikasi potensi phantom billing, biaya tersembunyi, atau harga yang tidak wajar.

## ✨ Fitur Utama

- 🔍 **OCR Processing** - Ekstraksi teks otomatis dari gambar/PDF tagihan
- 🤖 **AI-Powered Analysis** - Analisis item tagihan menggunakan Groq LLM
- 💰 **Currency Detection** - Deteksi dan konversi mata uang otomatis (USD/IDR)
- 🚨 **Risk Detection** - Identifikasi phantom billing dan biaya mencurigakan
- 📊 **Categorization** - Pengelompokan item berdasarkan tingkat risiko
- 🎨 **Modern UI** - Interface yang clean dan responsif

## 🛠️ Tech Stack

- **Backend:** Laravel 11.x, PHP 8.2+
- **Frontend:** Tailwind CSS 4, Vanilla JavaScript
- **OCR Service:** PaddleOCR (Python FastAPI)
- **AI Engine:** Groq API (Llama 3.1)
- **Database:** MySQL 8.0+
- **Asset Bundler:** Vite

---

## 📋 Prerequisites

Pastikan sistem Anda sudah terinstal:

- [PHP 8.2+](https://www.php.net/downloads) dengan extensions: `mbstring`, `xml`, `curl`, `mysql`, `zip`, `gd`
- [Composer 2.x](https://getcomposer.org/download/)
- [Node.js 18+ & npm 9+](https://nodejs.org/)
- [MySQL 8.0+](https://dev.mysql.com/downloads/)
- [Git](https://git-scm.com/downloads)
- [Python 3.10+](https://www.python.org/downloads/) (untuk OCR service)
- [pip](https://pip.pypa.io/en/stable/installation/) (Python package manager)

---

## 🚀 Installation Guide

### 1. Clone Repository

```bash
# Clone main application
git clone https://github.com/yourusername/billcheck.git
cd billcheck
```

### 2. Install Dependencies

#### Backend Dependencies
```bash
# Install PHP dependencies via Composer
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Frontend Dependencies
```bash
# Install Node.js dependencies
npm install
```

### 3. Database Setup

```bash
# Create database
mysql -u root -p
```

```sql
CREATE DATABASE bill_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Konfigurasi `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bill_db
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

```bash
# Run migrations
php artisan migrate
```

### 4. Setup OCR Service (PaddleOCR)

#### Clone OCR Repository
```bash
# Di directory terpisah (di luar project Laravel)
cd ..
git clone https://github.com/KateKateline/paddle-ocr-service.git
cd paddle-ocr-service
```

#### Install Python Dependencies
```bash
# Buat virtual environment (recommended)
python -m venv venv

# Aktifkan virtual environment
# Windows:
venv\Scripts\activate
# Linux/Mac:
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt
```

#### Run OCR Service
```bash
# Jalankan FastAPI server
python main.py

# Atau gunakan uvicorn
uvicorn main:app --host 127.0.0.1 --port 8000 --reload
```

**Note:** OCR service akan berjalan di `http://127.0.0.1:8000`

#### Verifikasi OCR Service
```bash
# Test endpoint
curl http://127.0.0.1:8000/health

# Expected response:
# {"status": "healthy", "service": "PaddleOCR"}
```

### 5. Configure API Keys

Edit file `.env` dan tambahkan:

```env
# OCR Service Configuration
OCR_SERVICE_URL=http://127.0.0.1:8000/ocr

# Groq AI Configuration
GROQ_API_KEY=your_groq_api_key_here
```

#### Mendapatkan Groq API Key:
1. Kunjungi [console.groq.com](https://console.groq.com)
2. Sign up/Login
3. Navigate ke **API Keys** section
4. Klik **Create API Key**
5. Copy key dan paste ke `.env`

### 6. Storage Setup

```bash
# Create symbolic link untuk storage
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

### 7. Build Assets

```bash
# Development build (with watch mode)
npm run dev

# Production build
npm run build
```

---

## 🎯 Running the Application

### Terminal 1: Laravel Development Server
```bash
cd billcheck
php artisan serve
```
App akan berjalan di: `http://127.0.0.1:8000`

### Terminal 2: OCR Service
```bash
cd paddle-ocr-service
source venv/bin/activate  # atau venv\Scripts\activate di Windows
python main.py
```
OCR service akan berjalan di: `http://127.0.0.1:8000`

### Terminal 3: Vite Dev Server (Optional, untuk development)
```bash
cd billcheck
npm run dev
```

---

## 📁 Project Structure

```
billcheck/
├── app/
│   ├── Actions/              # Business logic actions
│   ├── Http/Controllers/     # HTTP controllers
│   ├── Models/              # Eloquent models
│   └── Services/            # Service classes
│       ├── Ai/              # AI analysis services
│       ├── Ocr/             # OCR integration
│       ├── BillValidator.php
│       └── CurrencyConverter.php
├── database/
│   └── migrations/          # Database migrations
├── resources/
│   ├── css/                 # Tailwind CSS
│   ├── js/                  # JavaScript files
│   └── views/               # Blade templates
├── routes/
│   └── web.php              # Web routes
├── public/                  # Public assets
└── storage/                 # File storage
```

---

## 🔧 Configuration

### Environment Variables

```env
# App Configuration
APP_NAME=BillCheck
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bill_db
DB_USERNAME=root
DB_PASSWORD=

# OCR Service
OCR_SERVICE_URL=http://127.0.0.1:8000/ocr

# Groq AI
GROQ_API_KEY=your_groq_api_key

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120

# File Storage
FILESYSTEM_DISK=local

# Queue (optional)
QUEUE_CONNECTION=database
```

---

## 🧪 Testing

### Test Upload & OCR
1. Akses `http://127.0.0.1:8000`
2. Upload sample bill (PNG/JPG/PDF)
3. Tunggu OCR processing selesai
4. Klik "Analisis dengan AI"

### Test API Endpoints
```bash
# Health check
curl http://127.0.0.1:8000/api/health

# Upload test (requires file)
curl -X POST http://127.0.0.1:8000/bill/upload \
  -F "bill_file=@/path/to/sample-bill.jpg"
```

---

## 🐛 Troubleshooting

### Issue: OCR Service Not Running
**Solution:**
```bash
cd paddle-ocr-service
source venv/bin/activate
python main.py
```

### Issue: "GROQ_API_KEY is not configured"
**Solution:**
- Pastikan `.env` berisi `GROQ_API_KEY`
- Restart Laravel server: `php artisan serve`

### Issue: Database Connection Failed
**Solution:**
```bash
# Cek MySQL service
sudo systemctl status mysql  # Linux
# atau
net start MySQL80            # Windows

# Test connection
mysql -u root -p
```

### Issue: Storage Permission Denied
**Solution:**
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows: Run as Administrator
```

### Issue: npm/Vite Build Errors
**Solution:**
```bash
# Clear cache dan reinstall
rm -rf node_modules package-lock.json
npm install
npm run build
```

---

## 📝 API Documentation

### POST `/bill/upload`
Upload bill file for OCR processing

**Request:**
```
Content-Type: multipart/form-data
- bill_file: File (jpg|jpeg|png|pdf, max 5MB)
```

**Response:**
```
Redirect to: /bill/{uuid}
```

### GET `/bill/{uuid}`
View bill details and OCR results

**Response:** HTML view with bill data

### POST `/bill/{uuid}/analyze`
Analyze bill with AI

**Response:**
```
Redirect to: /bill/{uuid} (with analysis results)
```

---

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the MIT License - see [LICENSE](LICENSE) file for details.

---

## 👤 Author

**Your Name**
- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

---

## 🙏 Acknowledgments

- [PaddleOCR](https://github.com/PaddlePaddle/PaddleOCR) - OCR engine
- [Groq](https://groq.com) - AI inference platform
- [Laravel](https://laravel.com) - PHP framework
- [Tailwind CSS](https://tailwindcss.com) - CSS framework

---

## 📞 Support

Jika mengalami masalah atau memiliki pertanyaan:
- 📧 Email: support@billcheck.com
- 💬 Discord: [Join our server](https://discord.gg/your-invite)
- 📚 Docs: [Read the docs](https://docs.billcheck.com)

---

**Made with ❤️ by BillCheck Team**
