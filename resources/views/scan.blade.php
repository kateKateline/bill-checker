@extends('layout.app')

@section('content')
<section class="max-w-4xl mx-auto px-6 py-20 text-center">

    <h2 class="text-3xl font-bold mb-4 text-slate-800">
        Check Your Hospital Bill
    </h2>

    <p class="text-slate-500 max-w-xl mx-auto mb-12">
        Upload your hospital billing receipt and let AI help identify
        potential irregular or phantom charges.
    </p>

    @include('components.upload-box')

    <!-- LOADING -->
    <div id="loadingState" class="hidden mt-12">
        <div class="flex justify-center mb-4">
            <div class="w-10 h-10 border-4 border-emerald-400 border-t-transparent rounded-full animate-spin"></div>
        </div>
        <p class="text-slate-500">Analyzing your bill...</p>
    </div>

</section>

<!-- RESULT -->
<section id="resultState" class="hidden max-w-4xl mx-auto px-6 pb-24">

    <h3 class="text-xl font-semibold mb-6 text-[#4A70A9]">
        Analysis Result
    </h3>

    <div class="space-y-4">

        @include('components.bill-row', [
            'id' => 'row1',
            'itemName' => 'Complete Blood Test',
            'category' => 'Laboratory Examination',
            'price' => '450.000',
            'status' => 'safe',
            'label' => 'Normal',
            'description' => 'This charge aligns with standard laboratory pricing based on diagnosis.'
        ])

        @include('components.bill-row', [
            'id' => 'row2',
            'itemName' => 'Paracetamol Injection',
            'category' => 'Medication',
            'price' => '120.000',
            'status' => 'review',
            'label' => 'Needs Review',
            'description' => 'Price is slightly higher than average for similar cases.'
        ])

        @include('components.bill-row', [
            'id' => 'row3',
            'itemName' => 'Minor Surgical Procedure',
            'category' => 'Medical Procedure',
            'price' => '1.200.000',
            'status' => 'danger',
            'label' => 'Potential Phantom Billing',
            'description' => 'Procedure is not strongly supported by patient diagnosis data.'
        ])

    </div>
</section>

@endsection
