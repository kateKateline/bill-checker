<div class="bg-white rounded-lg border border-slate-200 overflow-hidden hover:shadow-sm transition-all group">
    <div class="flex items-center justify-between p-3.5 cursor-pointer select-none" onclick="toggleDetail('{{ $id }}')">
        
        <div class="flex items-center gap-3">
            <div class="w-2.5 h-2.5 rounded-full 
                {{ $status === 'safe' ? 'bg-emerald-400 ring-4 ring-emerald-50' : '' }}
                {{ $status === 'review' ? 'bg-amber-400 ring-4 ring-amber-50' : '' }}
                {{ $status === 'danger' ? 'bg-rose-500 ring-4 ring-rose-50' : '' }}">
            </div>
            
            <div>
                <p class="text-[13px] font-bold text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $itemName }}</p>
                <p class="text-[10px] font-medium text-slate-400 uppercase tracking-tight mt-0.5">{{ $category }}</p>
            </div>
        </div>

        <div class="text-right flex items-center gap-4">
            <div class="hidden sm:block">
                <p class="text-[13px] font-bold text-slate-900"> {{ $price }}</p>
                <p class="text-[9px] font-extrabold uppercase tracking-tighter
                    {{ $status === 'safe' ? 'text-emerald-600' : '' }}
                    {{ $status === 'review' ? 'text-amber-600' : '' }}
                    {{ $status === 'danger' ? 'text-rose-600' : '' }}">
                    {{ $label }}
                </p>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-400 transition-transform duration-200" id="icon-{{ $id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    <div id="{{ $id }}" class="hidden animate-fadeIn bg-slate-50/50 border-t border-slate-100">
        <div class="p-4 space-y-3">
            <div class="flex flex-wrap gap-4 text-[11px] border-b border-slate-100 pb-3">
                <div class="flex flex-col">
                    <span class="text-slate-400 uppercase font-bold tracking-tighter">Kategori</span>
                    <span class="text-slate-700 font-semibold">{{ $category }}</span>
                </div>
                <div class="flex flex-col border-l border-slate-200 pl-4">
                    <span class="text-slate-400 uppercase font-bold tracking-tighter">Nominal</span>
                    <span class="text-slate-700 font-bold "> {{ $price }}</span>
                </div>
            </div>

            <div class="bg-white p-3 rounded-md border border-slate-200/60 shadow-sm">
                <div class="flex items-center gap-1.5 mb-1 text-indigo-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <span class="text-[10px] font-black uppercase tracking-widest">Penjelasan Analisis</span>
                </div>
                <p class="text-xs leading-relaxed text-slate-600 italic">
                    "{{ $description }}"
                </p>
            </div>
        </div>
    </div>
</div>