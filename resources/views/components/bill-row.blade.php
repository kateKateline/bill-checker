<div class="bg-white rounded-lg border border-black/10 p-5">

    <!-- HEADER -->
    <div class="flex justify-between items-start cursor-pointer"
         onclick="toggleDetail('{{ $id }}')">

        <div>
            <p class="font-semibold text-black">
                {{ $itemName }}
            </p>
            <p class="text-sm text-black/60">
                {{ $category }}
            </p>
        </div>

        <div class="text-right">
            <p class="font-semibold">
                Rp {{ $price }}
            </p>
            <span class="
                text-sm font-semibold
                {{ $status === 'safe' ? 'text-[#4A70A9]' : '' }}
                {{ $status === 'review' ? 'text-[#8FABD4]' : '' }}
                {{ $status === 'danger' ? 'text-red-600' : '' }}
            ">
                {{ $label }}
            </span>
        </div>
    </div>

    <!-- DETAIL -->
    <div id="{{ $id }}" class="hidden mt-4 text-sm text-black/70 border-t pt-4">
        <p class="font-medium mb-1">AI Explanation</p>
        <p>{{ $description }}</p>
    </div>

</div>
