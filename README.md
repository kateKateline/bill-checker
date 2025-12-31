# 🏥 BillCheck - Hospital Bill Transparency Analyzer

**BillCheck** adalah aplikasi web yang membantu menganalisis transparansi tagihan rumah sakit menggunakan teknologi OCR (Optical Character Recognition) dan AI untuk mengidentifikasi potensi phantom billing, biaya tersembunyi, atau harga yang tidak wajar.

## ✨ Fitur Utama

-  **OCR Processing** - Ekstraksi teks otomatis dari gambar/PDF tagihan
-  **AI-Powered Analysis** - Analisis item tagihan menggunakan Groq LLM
-  **Currency Detection** - Deteksi dan konversi mata uang otomatis (USD/IDR)
-  **Risk Detection** - Identifikasi phantom billing dan biaya mencurigakan
-  **Categorization** - Pengelompokan item berdasarkan tingkat risiko
-  **Modern UI** - Interface yang clean dan responsif

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

Aplikasi ini membutuhkan **PaddleOCR Service** yang berjalan terpisah untuk melakukan ekstraksi teks dari gambar.

#### Clone OCR Repository
```bash
# Di directory terpisah (di luar project Laravel)
cd ..
git clone https://github.com/KateKateline/paddle-ocr-service.git
cd paddle-ocr-service
```

** Untuk instalasi dan konfigurasi lengkap OCR service, silakan ikuti panduan di repository:**
 [paddle-ocr-service](https://github.com/KateKateline/paddle-ocr-service)

**Note:** Pastikan OCR service sudah berjalan di `http://127.0.0.1:8000` sebelum menjalankan aplikasi BillCheck.

### 5. Configure API Keys

Edit file `.env` dan tambahkan:

```env

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

```

### 7. Build Assets

```bash
# Development build (with watch mode)
npm run dev

```

---

## 🎯 Running the Application

### Terminal 1: Laravel Development Server
```bash
cd billcheck
php artisan serve --port=8001
```
App harus berjalan **SELAIN** di port: `http://127.0.0.1:8000`

### Terminal 2: OCR Service

Tutorial lengkap berada di [paddle-ocr-service](https://github.com/KateKateline/paddle-ocr-service)
```bash
cd paddle-ocr-service
py -3.10 -m venv venv
uvicorn app.main:app --reload

```
OCR service akan berjalan di: `http://127.0.0.1:8000`

### Terminal 3: Vite Dev Server
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
1. Akses `http://127.0.0.1:8001`
2. Upload sample bill (PNG/JPG/PDF)
3. Tunggu OCR processing selesai
4. Klik "Analisis dengan AI"

---
