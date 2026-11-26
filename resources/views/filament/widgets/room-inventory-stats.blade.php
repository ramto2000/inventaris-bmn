<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Inventaris Berkas per Ruangan
        </x-slot>

        @php
            $rooms = $this->getRoomsData();
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="px-3 py-2 text-left">Kode</th>
                        <th class="px-3 py-2 text-left">Ruangan</th>
                        <th class="px-3 py-2 text-left">Penanggung Jawab</th>
                        <th class="px-3 py-2 text-center">Total</th>
                        <th class="px-3 py-2 text-center">Masuk</th>
                        <th class="px-3 py-2 text-center">Proses</th>
                        <th class="px-3 py-2 text-center">Selesai</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($rooms as $room)
                        <tr class="border-b">
                            <td class="px-3 py-2">
                                {{ $room['code'] }}
                            </td>

                            <td class="px-3 py-2 font-medium">
                                {{ $room['name'] }}
                            </td>

                            <td class="px-3 py-2">
                                {{ $room['pj'] }}
                            </td>

                            <td class="px-3 py-2 text-center font-bold">
                                {{ $room['total_assets'] }}
                            </td>

                            <td class="px-3 py-2 text-center">
                                {{ $room['baik'] }}
                            </td>

                            <td class="px-3 py-2 text-center">
                                {{ $room['rusak_ringan'] }}
                            </td>

                            <td class="px-3 py-2 text-center">
                                {{ $room['rusak_berat'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center">
                                Belum ada data ruangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
