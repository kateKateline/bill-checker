<form id="billUploadForm" action="{{ route('bill.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="bill_file" id="billFileInput" class="hidden" accept=".pdf,.jpg,.jpeg,.png">

    <div id="uploadBox" onclick="document.getElementById('billFileInput').click()"
        class="group relative bg-white border-2 border-dashed border-slate-300 rounded-2xl p-10 text-center hover:border-indigo-500 hover:bg-indigo-50/30 transition-all duration-300 cursor-pointer">
        
        <div id="uploadContent">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>

            <h3 class="text-lg font-bold text-slate-800 mb-1 transition-colors">Unggah Tagihan Rumah Sakit</h3>
            <p class="text-sm text-slate-500 mb-0">Klik untuk memilih file atau tarik ke sini</p>
            <p class="text-[11px] text-slate-400 mt-4 uppercase tracking-[0.1em] font-medium">PNG, JPG, PDF (Max 10MB)</p>
        </div>

        <div id="localPreview" class="hidden absolute inset-0 bg-white/90 backdrop-blur-sm rounded-2xl flex items-center justify-center p-4 animate-in fade-in duration-300">
            <div class="flex items-center gap-4 bg-white p-3 rounded-xl shadow-lg border border-slate-100">
                <div class="w-12 h-12 rounded-lg bg-indigo-50 overflow-hidden border border-indigo-100">
                    <img id="previewImage" src="#" class="hidden w-full h-full object-cover">
                    <div id="previewPdfIcon" class="hidden w-full h-full flex items-center justify-center text-rose-500">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18V2h12v4h4v12H4zM14 4H6v12h8V4z"/></svg>
                    </div>
                </div>
                <div class="text-left">
                    <p id="previewFileName" class="text-xs font-bold text-slate-800 truncate w-32"></p>
                    <p class="text-[10px] text-indigo-600 font-semibold animate-pulse uppercase">Sedang Memuat...</p>
                </div>
            </div>
        </div>
    </div>
</form>