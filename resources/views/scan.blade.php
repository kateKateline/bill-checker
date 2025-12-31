@extends('layout.app')

@section('content')

<section class="bg-linear-to-b from-indigo-50/60 to-slate-50 pt-20 pb-16 px-6">
  <div class="max-w-4xl mx-auto text-center">

    <h2 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-4 tracking-tight">
      Cek Transparansi Tagihan Rumah Sakit
    </h2>

    <p class="text-slate-600 text-sm md:text-base max-w-xl mx-auto mb-12 leading-relaxed">
      Unggah foto kuitansi atau rincian biaya rumah sakit Anda.
      Sistem analisis akan membantu mengidentifikasi potensi biaya tersembunyi
      atau harga yang tidak wajar.
    </p>

  </div>

  <div class="max-w-xl mx-auto" id="uploadContainer">
    <!-- Upload Box -->
    <div id="uploadBoxWrapper" class="{{ isset($bill) && !$bill->isAnalyzed() ? 'hidden' : '' }}">
      @include('components.upload-box')
    </div>

    <!-- File Preview (After OCR) -->
    @if (isset($bill) && $bill->isOcrCompleted() && !$bill->isAnalyzed())
    <div id="filePreviewWrapper" class="bg-white border-2 border-indigo-200 rounded-xl p-4 shadow-lg">
      <div class="relative group">
        @php
          $fileUrl = asset('storage/' . $bill->file_path);
          $isImage = in_array(strtolower(pathinfo($bill->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
        @endphp
        
        @if ($isImage)
          <img 
            src="{{ $fileUrl }}" 
            alt="Uploaded bill" 
            class="w-full h-auto rounded-lg object-contain max-h-48 mx-auto"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
          >
          <div class="hidden w-full h-32 bg-slate-100 rounded-lg flex items-center justify-center">
            <div class="text-center">
              <svg class="w-12 h-12 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-xs text-slate-500">Preview tidak tersedia</p>
            </div>
          </div>
        @else
          <div class="w-full h-32 bg-slate-100 rounded-lg flex items-center justify-center">
            <div class="text-center">
              <svg class="w-12 h-12 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-xs font-semibold text-slate-700 mb-1">File PDF</p>
              <p class="text-xs text-slate-500">{{ basename($bill->file_path) }}</p>
            </div>
          </div>
        @endif
        
      </div>
      
      @if (isset($isValidBill) && !$isValidBill)
      <div class="mt-4 bg-amber-50 border-2 border-amber-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <div>
            <h4 class="font-semibold text-amber-900 mb-1 text-sm">
              @if (isset($hasNoText) && $hasNoText)
                Tidak Ada Teks Terdeteksi pada File
              @else
                File yang Diupload Bukan Tagihan Rumah Sakit
              @endif
            </h4>
            <p class="text-xs text-amber-700 leading-relaxed">
              @if (isset($hasNoText) && $hasNoText)
                File yang Anda upload tidak mengandung teks yang dapat dibaca. Pastikan file yang diupload adalah dokumen tagihan rumah sakit yang jelas dan dapat dibaca.
              @else
                File yang Anda upload tidak terdeteksi sebagai tagihan atau kuitansi rumah sakit. 
                Pastikan Anda mengupload dokumen tagihan medis yang valid untuk mendapatkan hasil analisis yang akurat.
              @endif
            </p>
          </div>
        </div>
      </div>
      @endif
    </div>
    @endif

    <!-- OCR Loading State -->
    <div id="ocrLoadingState" class="hidden">
      <div class="group bg-white border-2 border-indigo-300 rounded-xl p-8 text-center shadow-lg">
        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <div class="animate-spin w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full"></div>
        </div>
        
        <h3 class="text-lg font-bold text-slate-800 mb-2">Memproses <span id="ocrFileType">File</span>...</h3>
        
        <p id="ocrStatusText" class="text-sm text-slate-500 mb-4">Memulai pemrosesan...</p>
        
        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden mb-2">
          <div id="ocrProgressBar" class="h-full bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full transition-all duration-500 ease-out shadow-sm" style="width: 0%"></div>
        </div>
        
        <p id="ocrProgressText" class="text-xs font-semibold text-indigo-600">0%</p>
        
        <div class="mt-4 flex justify-center gap-2">
          <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
          <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
          <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        </div>
      </div>
    </div>

    <!-- AI Analysis Loading State -->
    <div id="aiLoadingState" class="hidden">
      <div class="group bg-white border-2 border-indigo-300 rounded-xl p-8 text-center shadow-lg">
        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
          </svg>
        </div>
        
        <h3 class="text-lg font-bold text-slate-800 mb-2">Menganalisis dengan AI...</h3>
        
        <p id="aiProgressText" class="text-sm text-slate-500 mb-4">Memulai analisis...</p>
        
        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden mb-2">
          <div id="aiProgressBar" class="h-full bg-gradient-to-r from-indigo-500 via-indigo-600 to-purple-600 rounded-full transition-all duration-500 ease-out shadow-sm animate-pulse" style="width: 0%"></div>
        </div>
        
        <p id="aiProgressPercent" class="text-xs font-semibold text-indigo-600">0%</p>
        
        <div class="mt-4 flex justify-center gap-2">
          <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
          <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
          <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        </div>
      </div>
    </div>
  </div>

  @if (isset($bill) && $bill->hasRawText() && !$bill->isAnalyzed() && ($isValidBill ?? true))
  <form id="analyzeForm" action="{{ route('bill.analyze', $bill) }}" method="POST" class="text-center mt-8">
    @csrf
    <button
        type="submit"
        id="analyzeButton"
        class="px-8 py-3 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition shadow-lg hover:shadow-xl transform hover:scale-105"
    >
        <span class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
          </svg>
          Analisis dengan AI
        </span>
    </button>
    <p class="text-xs text-slate-500 mt-2">Analisis tagihan menggunakan analisis untuk mengidentifikasi potensi masalah</p>
  </form>
  @endif


</section>


@php
  $hasResults = isset($groupedResults) && isset($bill) && $bill->isAnalyzed();
  $totalItems = 0;
  if ($hasResults) {
    foreach (['danger', 'review', 'safe'] as $status) {
      if (isset($groupedResults[$status])) {
        $totalItems += $groupedResults[$status]->count();
      }
    }
  }
  $isValidBill = $isValidBill ?? true;
@endphp

@if ($hasResults && $totalItems > 0)
<section id="resultState" class="max-w-4xl mx-auto px-6 py-12">

  <div class="flex items-center gap-2 mb-8 border-b border-slate-200 pb-4">
    <div class="w-1.5 h-6 bg-indigo-600 rounded-full"></div>
    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Hasil Analisa</h3>
  </div>

  @php
    $statusLabels = [
      'danger' => ['label' => 'Perlu Perhatian', 'color' => 'rose', 'icon' => ''],
      'review' => ['label' => 'Perlu Ditinjau', 'color' => 'amber', 'icon' => ''],
      'safe' => ['label' => 'Aman', 'color' => 'emerald', 'icon' => '']
    ];
  @endphp

  @foreach (['danger', 'review', 'safe'] as $status)
    @if (isset($groupedResults[$status]) && $groupedResults[$status]->count() > 0)
      <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
          @if ($status === 'danger')
            <div class="w-1 h-8 bg-rose-500 rounded-full"></div>
          @elseif ($status === 'review')
            <div class="w-1 h-8 bg-amber-500 rounded-full"></div>
          @else
            <div class="w-1 h-8 bg-emerald-500 rounded-full"></div>
          @endif
          <h4 class="text-base font-bold text-slate-800 flex items-center gap-2">
            <span>{{ $statusLabels[$status]['icon'] }}</span>
            {{ $statusLabels[$status]['label'] }}
            <span class="text-sm font-normal text-slate-500">({{ $groupedResults[$status]->count() }} item)</span>
          </h4>
        </div>
        <div class="space-y-3">
          @foreach ($groupedResults[$status] as $row)
            @include('components.bill-row', $row)
          @endforeach
        </div>
      </div>
    @endif
  @endforeach

</section>
@endif

@endsection