@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan data emisi dan absorpsi karbon Kota Bandar Lampung')

@section('year-filter')
<form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
    <label class="text-xs font-medium text-gray-500">Tahun:</label>
    <select name="tahun" onchange="this.form.submit()"
            class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none cursor-pointer">
        @foreach($availableYears as $year)
            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
        @endforeach
    </select>
</form>
@endsection

@section('content')
{{-- KPI Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    {{-- Total Emisi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg hover:shadow-red-500/5 transition-all duration-300">
        <div class="h-1 bg-gradient-to-r from-red-400 to-red-600"></div>
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Emisi</span>
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($kpi->total_emisi, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">ton CO₂ / tahun</p>
        </div>
    </div>

    {{-- Total Absorpsi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg hover:shadow-emerald-500/5 transition-all duration-300">
        <div class="h-1 bg-gradient-to-r from-emerald-400 to-emerald-600"></div>
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Absorpsi</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($kpi->total_absorpsi, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">ton CO₂ / tahun</p>
        </div>
    </div>

    {{-- Selisih Karbon --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg hover:shadow-amber-500/5 transition-all duration-300">
        <div class="h-1 bg-gradient-to-r from-amber-400 to-amber-600"></div>
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Selisih Karbon</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold {{ $kpi->selisih > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $kpi->selisih > 0 ? '+' : '' }}{{ number_format($kpi->selisih, 2, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">ton CO₂ (Emisi - Absorpsi)</p>
        </div>
    </div>

    {{-- Kredit Karbon --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg hover:shadow-purple-500/5 transition-all duration-300">
        <div class="h-1 bg-gradient-to-r from-purple-400 to-purple-600"></div>
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Kredit Karbon</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-purple-700">{{ number_format($kpi->kredit_karbon, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">ton CO₂ dibutuhkan</p>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">
    {{-- Emisi Chart --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Emisi CO₂ per Kecamatan</h3>
                <p class="text-xs text-gray-400">Tahun {{ $tahun }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <div style="height: 350px;">
            <canvas id="emisiChart"></canvas>
        </div>
    </div>

    {{-- Absorpsi Chart --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Absorpsi CO₂ per Kecamatan</h3>
                <p class="text-xs text-gray-400">Tahun {{ $tahun }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <div style="height: 350px;">
            <canvas id="absorpsiChart"></canvas>
        </div>
    </div>
</div>

{{-- Net Carbon Chart --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-bold text-gray-800">Net Carbon per Kecamatan</h3>
            <p class="text-xs text-gray-400">Tahun {{ $tahun }} — Positif = Defisit (merah), Negatif = Surplus (hijau)</p>
        </div>
        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
    </div>
    <div style="height: 500px;">
        <canvas id="netCarbonChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = @json($chartData->pluck('nama_kecamatan'));
    const emisiData = @json($chartData->pluck('emisi_co2'));
    const absorpsiData = @json($chartData->pluck('absorpsi_co2'));
    const netCarbonData = @json($chartData->pluck('net_carbon'));

    const chartFont = { family: 'Inter', size: 11 };

    // Emisi Bar Chart
    new Chart(document.getElementById('emisiChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Emisi CO₂ (ton)',
                data: emisiData,
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: chartFont,
                    bodyFont: chartFont,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => `Emisi: ${parseFloat(ctx.raw).toLocaleString('id-ID')} ton CO₂`
                    }
                }
            },
            scales: {
                x: {
                    ticks: { font: { ...chartFont, size: 9 }, maxRotation: 45, minRotation: 45 },
                    grid: { display: false }
                },
                y: {
                    ticks: { font: chartFont, callback: v => v.toLocaleString('id-ID') },
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    beginAtZero: true
                }
            }
        }
    });

    // Absorpsi Bar Chart
    new Chart(document.getElementById('absorpsiChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Absorpsi CO₂ (ton)',
                data: absorpsiData,
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: chartFont,
                    bodyFont: chartFont,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => `Absorpsi: ${parseFloat(ctx.raw).toLocaleString('id-ID')} ton CO₂`
                    }
                }
            },
            scales: {
                x: {
                    ticks: { font: { ...chartFont, size: 9 }, maxRotation: 45, minRotation: 45 },
                    grid: { display: false }
                },
                y: {
                    ticks: { font: chartFont, callback: v => v.toLocaleString('id-ID') },
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    beginAtZero: true
                }
            }
        }
    });

    // Net Carbon Horizontal Bar
    const netColors = netCarbonData.map(v => parseFloat(v) > 0 ? 'rgba(239, 68, 68, 0.7)' : 'rgba(16, 185, 129, 0.7)');
    const netBorders = netCarbonData.map(v => parseFloat(v) > 0 ? 'rgba(239, 68, 68, 1)' : 'rgba(16, 185, 129, 1)');

    new Chart(document.getElementById('netCarbonChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Net Carbon (ton CO₂)',
                data: netCarbonData,
                backgroundColor: netColors,
                borderColor: netBorders,
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: chartFont,
                    bodyFont: chartFont,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => {
                            const val = parseFloat(ctx.raw);
                            const status = val > 0 ? 'Defisit' : val < 0 ? 'Surplus' : 'Seimbang';
                            return `Net: ${val.toLocaleString('id-ID')} ton CO₂ (${status})`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { font: chartFont, callback: v => v.toLocaleString('id-ID') },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                y: {
                    ticks: { font: { ...chartFont, size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
