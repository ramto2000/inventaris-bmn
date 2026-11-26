@php
    use App\Models\Asset;

    $routeRecord = request()->route('record');

    // Jika route sudah melakukan model binding
    if ($routeRecord instanceof Asset) {
        $record = $routeRecord;
    } else {
        $record = Asset::with([
            'mutations',
            'maintenances',
            'loans.user',
        ])->find($routeRecord);
    }

    $history = collect();

    /*
    |--------------------------------------------------------------------------
    | 1. DATA PEROLEHAN
    |--------------------------------------------------------------------------
    */

    if ($record?->tanggal_perolehan) {
        $history->push([
            'date' => $record->tanggal_perolehan,
            'title' => 'Barang Terdaftar (Perolehan)',
            'description' =>
                'Aset masuk ke sistem dengan harga Rp ' .
                number_format($record->harga_perolehan ?? 0, 0, ',', '.'),
            'icon' => 'heroicon-m-shopping-cart',
            'color' => 'bg-emerald-500',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. DATA MUTASI
    |--------------------------------------------------------------------------
    */

    foreach ($record?->mutations ?? [] as $mutation) {
        $history->push([
            'date' => $mutation->created_at,
            'title' => 'Mutasi Aset',
            'description' =>
                $mutation->keterangan ?? 'Perubahan / mutasi aset',
            'icon' => 'heroicon-m-arrow-path',
            'color' => 'bg-cyan-500',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. DATA PEMELIHARAAN
    |--------------------------------------------------------------------------
    */

    foreach (
        $record?->maintenances?->where('status', 'completed') ?? []
        as $maintenance
    ) {
        $history->push([
            'date' => $maintenance->tanggal_selesai,
            'title' => 'Pemeliharaan Selesai',
            'description' =>
                'Selesai diperbaiki: ' .
                ($maintenance->masalah ?? '-') .
                '. Biaya: Rp ' .
                number_format($maintenance->biaya ?? 0, 0, ',', '.'),
            'icon' => 'heroicon-m-wrench-screwdriver',
            'color' => 'bg-amber-500',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. DATA PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    foreach ($record?->loans ?? [] as $loan) {
        $namaPeminjam = $loan->user?->name ?? 'Tidak diketahui';

        $tanggalKembali = $loan->tanggal_kembali_rencana
            ? \Carbon\Carbon::parse(
                $loan->tanggal_kembali_rencana
            )->format('d/m/Y')
            : '-';

        $history->push([
            'date' => $loan->tanggal_pinjam,
            'title' => 'Aset Dipinjam',
            'description' =>
                "Dipinjam oleh {$namaPeminjam}. " .
                "Rencana kembali: {$tanggalKembali}",
            'icon' => 'heroicon-m-user-group',
            'color' => 'bg-purple-500',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | URUTKAN TERBARU
    |--------------------------------------------------------------------------
    */

    $sortedHistory = $history
        ->filter(fn ($item) => !empty($item['date']))
        ->sortByDesc('date');
@endphp

@if ($record)

    <div
        class="space-y-6 relative
        before:absolute
        before:inset-0
        before:ml-5
        before:-translate-x-px
        md:before:mx-auto
        md:before:translate-x-0
        before:h-full
        before:w-0.5
        before:bg-gradient-to-b
        before:from-transparent
        before:via-slate-300
        before:to-transparent"
    >

        @forelse ($sortedHistory as $item)

            <div
                class="relative flex items-center justify-between
                md:justify-normal
                md:odd:flex-row-reverse
                group"
            >

                {{-- ICON --}}
                <div
                    class="flex items-center justify-center
                    w-10 h-10 rounded-full
                    border border-white
                    shadow
                    shrink-0
                    md:order-1
                    md:group-odd:-translate-x-1/2
                    md:group-even:translate-x-1/2
                    {{ $item['color'] }}
                    text-white"
                >
                    @svg($item['icon'], 'w-5 h-5')
                </div>

                {{-- CONTENT --}}
                <div
                    class="w-[calc(100%-4rem)]
                    md:w-[calc(50%-2.5rem)]
                    p-4
                    rounded-xl
                    border border-slate-200
                    bg-white
                    shadow-sm"
                >

                    <div
                        class="flex items-center justify-between
                        gap-3 mb-1"
                    >
                        <div class="font-bold text-slate-900">
                            {{ $item['title'] }}
                        </div>

                        <time
                            class="font-medium text-cyan-600 text-xs whitespace-nowrap"
                        >
                            {{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}
                        </time>
                    </div>

                    <div class="text-slate-500 text-sm italic">
                        {{ $item['description'] }}
                    </div>

                </div>

            </div>

        @empty

            <div class="text-center py-8 text-slate-500">
                Belum ada riwayat aktivitas untuk aset ini.
            </div>

        @endforelse

    </div>

@else

    <div class="text-center py-8 text-slate-500">
        Data aset tidak ditemukan.
    </div>

@endif
