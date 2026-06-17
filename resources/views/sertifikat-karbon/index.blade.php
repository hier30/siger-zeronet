@extends('layouts.app')

@section('title', 'Sertifikat Karbon')
@section('page-title', 'Sertifikat Karbon')
@section('page-subtitle', 'CRUD sertifikat serapan karbon')

@section('content')
<div class="space-y-5">
    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-100 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 p-5 border-b border-gray-100">
            <form method="GET" action="{{ route('sertifikat-karbon.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari pemilik/wilayah..."
                           class="text-sm pl-9 pr-4 py-2 border border-gray-200 rounded-lg w-full sm:w-64 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                </div>

                <select name="tahun"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    <option value="">Semua tahun</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" @selected((string) $tahun === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>

                <select name="status"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    <option value="">Semua status</option>
                    @foreach($statuses as $statusOption)
                        <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ $statusOption }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-900">
                    Filter
                </button>
            </form>

            <a href="{{ route('sertifikat-karbon.create', request()->only(['tahun', 'status', 'search'])) }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006954] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#004236]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Sertifikat
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-5 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider w-12">No</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Pemilik</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider">Wilayah</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">Luas Lahan</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">Serapan</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-500 text-xs uppercase tracking-wider">Tahun</th>
                        <th class="px-5 py-3 text-center font-semibold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sertifikats as $sertifikat)
                        <tr class="border-b border-gray-50 hover:bg-emerald-50/30 transition-colors duration-150">
                            <td class="px-5 py-3.5 text-gray-400 font-medium">
                                {{ $sertifikats->firstItem() + $loop->index }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-gray-800">
                                {{ $sertifikat->nama_pemilik }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                {{ $sertifikat->wilayah }}
                            </td>
                            <td class="px-5 py-3.5 text-right text-gray-700 font-medium">
                                {{ number_format((float) $sertifikat->luas_lahan, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right text-emerald-600 font-semibold">
                                {{ number_format((float) $sertifikat->jumlah_serapan, 2, ',', '.') }} {{ $sertifikat->satuan }}
                            </td>
                            <td class="px-5 py-3.5 text-center text-gray-600 font-medium">
                                {{ $sertifikat->tahun }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if ($sertifikat->status === 'Aktif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Aktif</span>
                                @elseif ($sertifikat->status === 'Menunggu Verifikasi')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Menunggu</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('sertifikat-karbon.edit', array_merge(['sertifikatKarbon' => $sertifikat], request()->only(['tahun', 'status', 'search']))) }}"
                                       class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('sertifikat-karbon.destroy', $sertifikat) }}" onsubmit="return confirm('Hapus sertifikat karbon ini?')">
                                        @csrf
                                        @method('DELETE')
                                        @foreach(request()->only(['tahun', 'status', 'search']) as $key => $value)
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
                                Data sertifikat karbon tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100">
            {{ $sertifikats->links() }}
        </div>
    </div>
</div>
@endsection
