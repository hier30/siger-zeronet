@extends('layouts.app')

@section('title', 'Kelola Data Carbon')
@section('page-title', 'Kelola Data Carbon')
@section('page-subtitle', 'CRUD data emisi dan absorpsi karbon')

@section('content')
<div class="space-y-5">
    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 border-b border-gray-100">
            <form method="GET" action="{{ route('data-carbon.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kecamatan..."
                           class="text-sm pl-9 pr-4 py-2 border border-gray-200 rounded-lg w-full sm:w-64 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                </div>

                <select name="tahun"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    <option value="">Semua tahun</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" @selected((string) $tahun === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-900">
                    Filter
                </button>
            </form>

            <a href="{{ route('data-carbon.create', request()->only(['tahun', 'search'])) }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006954] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#004236]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Data
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-5 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Kecamatan</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-500 text-xs uppercase tracking-wider">Tahun</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">Emisi CO2</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">Absorpsi CO2</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">Net Carbon</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carbonData as $item)
                        <tr class="border-b border-gray-50 hover:bg-emerald-50/30 transition-colors duration-150">
                            <td class="px-5 py-3.5 text-gray-400 font-medium">
                                {{ $carbonData->firstItem() + $loop->index }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-gray-800">
                                {{ $item->kecamatan?->kecamatan ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center text-gray-600 font-medium">
                                {{ $item->tahun }}
                            </td>
                            <td class="px-5 py-3.5 text-right text-red-600 font-medium">
                                {{ number_format((float) $item->emisi_co2, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right text-emerald-600 font-medium">
                                {{ number_format((float) $item->absorpsi_co2, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold {{ $item->net_carbon > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ number_format((float) $item->net_carbon, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if ($item->status === 'Defisit Karbon')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Defisit</span>
                                @elseif ($item->status === 'Surplus Karbon')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Surplus</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Seimbang</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('data-carbon.edit', array_merge(['carbonData' => $item], request()->only(['tahun', 'search']))) }}"
                                       class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('data-carbon.destroy', $item) }}" onsubmit="return confirm('Hapus data carbon ini?')">
                                        @csrf
                                        @method('DELETE')
                                        @foreach(request()->only(['tahun', 'search']) as $key => $value)
                                            <input type="hidden" name="return_{{ $key }}" value="{{ $value }}">
                                        @endforeach
                                        <button type="submit"
                                                class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                Data carbon tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100">
            {{ $carbonData->links() }}
        </div>
    </div>
</div>
@endsection
