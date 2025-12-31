// Upload Box Preview and Form Submission
document.addEventListener('DOMContentLoaded', function() {
  const billFileInput = document.getElementById('billFileInput');
  
  if (billFileInput) {
    billFileInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      const form = document.getElementById('billUploadForm');
      const ocrLoading = document.getElementById('ocrLoadingState');
      const uploadBoxWrapper = document.getElementById('uploadBoxWrapper');
      
      const localPreview = document.getElementById('localPreview');
      const previewImage = document.getElementById('previewImage');
      const previewPdfIcon = document.getElementById('previewPdfIcon');
      const previewFileName = document.getElementById('previewFileName');

      if (file) {
        // Show local preview
        if (localPreview) {
          localPreview.classList.remove('hidden', 'opacity-0');
          localPreview.classList.add('flex', 'opacity-100');
        }
        if (previewFileName) {
          previewFileName.textContent = file.name;
        }

        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = (e) => {
            if (previewImage) {
              previewImage.src = e.target.result;
              previewImage.classList.remove('hidden');
            }
            if (previewPdfIcon) {
              previewPdfIcon.classList.add('hidden');
            }
          }
          reader.readAsDataURL(file);
        } else {
          if (previewImage) {
            previewImage.classList.add('hidden');
          }
          if (previewPdfIcon) {
            previewPdfIcon.classList.remove('hidden');
          }
        }

        // Delay then submit
        setTimeout(() => {
          if (uploadBoxWrapper) {
            uploadBoxWrapper.classList.add('hidden');
          }
          if (ocrLoading) {
            ocrLoading.classList.remove('hidden');
          }
          
          let progress = 0;
          const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            const progressBar = document.getElementById('ocrProgressBar');
            const progressText = document.getElementById('ocrProgressText');
            if (progressBar) progressBar.style.width = progress + '%';
            if (progressText) progressText.textContent = Math.round(progress) + '%';
          }, 300);
          
          if (form) {
            form.submit();
          }
        }, 1500);
      }
    });
  }

  // AI Analysis Form Submission (fallback for app.blade.php)
  const analyzeForm = document.getElementById('analyzeForm');
  
  if (analyzeForm) {
    analyzeForm.addEventListener('submit', function() {
      const filePreviewWrapper = document.getElementById('filePreviewWrapper');
      const aiLoading = document.getElementById('aiLoadingState');
      
      if (filePreviewWrapper) {
        filePreviewWrapper.classList.add('hidden');
      }
      this.classList.add('hidden');
      if (aiLoading) {
        aiLoading.classList.remove('hidden');
      }
      
      let progress = 0;
      const interval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress > 85) progress = 85;
        const progressBar = document.getElementById('aiProgressBar');
        const progressPercent = document.getElementById('aiProgressPercent');
        if (progressBar) progressBar.style.width = progress + '%';
        if (progressPercent) progressPercent.textContent = Math.round(progress) + '%';
      }, 500);
    });
  }
});

// Toggle detail function (for bill items) - make it globally available
window.toggleDetail = function(id) {
  const element = document.getElementById(id);
  if (element) {
    element.classList.toggle('hidden');
  }
}

