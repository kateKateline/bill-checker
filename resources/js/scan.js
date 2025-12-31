// OCR Loading Animation with dynamic file type detection
document.addEventListener('DOMContentLoaded', function() {
  const billFileInput = document.getElementById('billFileInput');
  
  if (billFileInput) {
    billFileInput.addEventListener('change', function() {
      const form = document.getElementById('billUploadForm');
      const ocrLoading = document.getElementById('ocrLoadingState');
      const uploadBoxWrapper = document.getElementById('uploadBoxWrapper');
      const file = this.files[0];
      
      if (file) {
        // Detect file type
        const ext = file.name.split('.').pop().toUpperCase();
        const fileType = file.type.startsWith('image/') ? `Gambar ${ext}` : `File ${ext}`;
        
        // Hide upload box and show loading
        uploadBoxWrapper?.classList.add('hidden');
        ocrLoading?.classList.remove('hidden');
        
        // Set file type
        const fileTypeEl = document.getElementById('ocrFileType');
        if (fileTypeEl) fileTypeEl.textContent = fileType;
        
        // Dynamic loading messages based on file type
        const messages = file.type === 'application/pdf' 
          ? [
              'Membuka dokumen PDF...',
              'Mengekstrak halaman...',
              'Membaca teks dari PDF...',
              'Memproses konten dokumen...',
              'Menyelesaikan ekstraksi...'
            ]
          : [
              'Memindai gambar...',
              'Mendeteksi teks pada gambar...',
              'Menganalisis struktur dokumen...',
              'Mengekstrak informasi...',
              'Memverifikasi hasil OCR...'
            ];
        
        // Simulate progress with dynamic messages
        let progress = 0;
        let msgIndex = 0;
        const interval = setInterval(() => {
          progress += Math.random() * 15;
          if (progress > 90) progress = 90;
          
          const progressBar = document.getElementById('ocrProgressBar');
          const progressText = document.getElementById('ocrProgressText');
          const statusText = document.getElementById('ocrStatusText');
          
          if (progressBar) progressBar.style.width = progress + '%';
          if (progressText) progressText.textContent = Math.round(progress) + '%';
          
          // Update status message
          if (statusText) {
            const newIndex = Math.min(Math.floor(progress / 20), messages.length - 1);
            if (newIndex !== msgIndex) {
              msgIndex = newIndex;
              statusText.style.opacity = '0';
              setTimeout(() => {
                statusText.textContent = messages[msgIndex];
                statusText.style.opacity = '1';
              }, 150);
            }
          }
        }, 400);
        
        form?.submit();
      }
    });
  }

  // AI Analysis Loading Animation with enhanced messages
  const analyzeForm = document.getElementById('analyzeForm');
  
  if (analyzeForm) {
    analyzeForm.addEventListener('submit', function(e) {
      const aiLoading = document.getElementById('aiLoadingState');
      const filePreviewWrapper = document.getElementById('filePreviewWrapper');
      
      // Hide file preview and analyze button, show AI loading
      if (filePreviewWrapper) filePreviewWrapper.classList.add('hidden');
      analyzeForm.classList.add('hidden');
      aiLoading?.classList.remove('hidden');
      
      const messages = [
        'Memulai analisis...',
        'Mengidentifikasi item tagihan...',
        'Membandingkan dengan database harga...',
        'Mendeteksi anomali biaya...',
        'Mengkategorikan tingkat risiko...',
        'Menyusun rekomendasi...',
        'Menyelesaikan analisis...'
      ];
      
      // Simulate progress with smooth transitions
      let progress = 0;
      let msgIndex = 0;
      const interval = setInterval(() => {
        progress += Math.random() * 8 + 2; // Slower, more consistent progress
        if (progress > 90) progress = 90;
        
        const progressBar = document.getElementById('aiProgressBar');
        const progressText = document.getElementById('aiProgressText');
        const progressPercent = document.getElementById('aiProgressPercent');
        
        if (progressBar) progressBar.style.width = progress + '%';
        if (progressPercent) progressPercent.textContent = Math.round(progress) + '%';
        
        // Smooth message transitions
        if (progressText) {
          const newIndex = Math.min(Math.floor(progress / 13), messages.length - 1);
          if (newIndex !== msgIndex) {
            msgIndex = newIndex;
            progressText.style.opacity = '0';
            progressText.style.transition = 'opacity 0.2s ease';
            setTimeout(() => {
              progressText.textContent = messages[msgIndex];
              progressText.style.opacity = '1';
            }, 200);
          }
        }
      }, 600);
    });
  }
});

