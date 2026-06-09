@extends('layouts.app')

@section('title', 'Carbon Trade')
@section('page-title', 'Carbon Trade')
@section('page-subtitle', 'Analisis kredit karbon per kecamatan')

@section('year-filter')
<div class="flex items-center gap-2">
    <label class="text-xs font-medium text-gray-500">Tahun:</label>
    <select id="yearFilter"
            class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none cursor-pointer">
        @foreach($availableYears as $year)
            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
        @endforeach
    </select>
</div>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Table Toolbar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            {{-- Search --}}
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Cari kecamatan..."
                       class="text-sm pl-9 pr-4 py-2 border border-gray-200 rounded-lg w-64 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Export Excel --}}
            <button onclick="exportExcel()" id="btn-export-excel"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </button>
            {{-- Export PDF --}}
            <button onclick="exportPDF()" id="btn-export-pdf"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="carbonTable">
            <thead>
                <tr class="bg-gray-50/80">
                    <th class="px-5 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider w-12">No</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-500 text-xs uppercase tracking-wider cursor-pointer hover:text-gray-800 transition-colors select-none" data-sort="nama_kecamatan">
                        <div class="flex items-center gap-1">Nama Kecamatan <span class="sort-icon">↕</span></div>
                    </th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider cursor-pointer hover:text-gray-800 transition-colors select-none" data-sort="emisi_co2">
                        <div class="flex items-center justify-end gap-1">Emisi CO₂ <span class="sort-icon">↕</span></div>
                    </th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider cursor-pointer hover:text-gray-800 transition-colors select-none" data-sort="absorpsi_co2">
                        <div class="flex items-center justify-end gap-1">Absorpsi CO₂ <span class="sort-icon">↕</span></div>
                    </th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider cursor-pointer hover:text-gray-800 transition-colors select-none" data-sort="net_carbon">
                        <div class="flex items-center justify-end gap-1">Net Carbon <span class="sort-icon">↕</span></div>
                    </th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-500 text-xs uppercase tracking-wider">Kredit Karbon</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-8 h-8 animate-spin text-emerald-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memuat data...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
        <div class="text-xs text-gray-500" id="paginationInfo">Menampilkan 0 dari 0 data</div>
        <div class="flex items-center gap-1" id="paginationButtons"></div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5" id="summaryCards">
    <div class="bg-gradient-to-br from-red-50 to-red-100/50 rounded-2xl border border-red-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-red-600 uppercase tracking-wider">Defisit Karbon</span>
        </div>
        <p class="text-2xl font-bold text-red-700" id="defisitCount">0</p>
        <p class="text-xs text-red-400 mt-1">kecamatan</p>
    </div>
    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-2xl border border-emerald-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Surplus Karbon</span>
        </div>
        <p class="text-2xl font-bold text-emerald-700" id="surplusCount">0</p>
        <p class="text-xs text-emerald-400 mt-1">kecamatan</p>
    </div>
    <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-2xl border border-amber-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Seimbang</span>
        </div>
        <p class="text-2xl font-bold text-amber-700" id="seimbangCount">0</p>
        <p class="text-xs text-amber-400 mt-1">kecamatan</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let allData = [];
    let filteredData = [];
    let currentSort = { key: 'nama_kecamatan', dir: 'asc' };
    let currentPage = 1;
    const perPage = 10;

    const tableBody = document.getElementById('tableBody');
    const searchInput = document.getElementById('searchInput');
    const yearFilter = document.getElementById('yearFilter');

    // Load data
    function loadData() {
        const tahun = yearFilter.value;
        fetch(`/api/carbon-trade?tahun=${tahun}&per_page=100`)
            .then(res => res.json())
            .then(json => {
                allData = json.data.data || [];
                applyFilters();
            })
            .catch(err => {
                console.error('Error loading data:', err);
                tableBody.innerHTML = '<tr><td colspan="7" class="px-5 py-12 text-center text-red-400">Gagal memuat data</td></tr>';
            });
    }

    function applyFilters() {
        const search = searchInput.value.toLowerCase();
        filteredData = allData.filter(item =>
            item.nama_kecamatan.toLowerCase().includes(search)
        );
        sortData();
        currentPage = 1;
        renderTable();
        updateSummary();
    }

    function sortData() {
        const { key, dir } = currentSort;
        filteredData.sort((a, b) => {
            let aVal = a[key];
            let bVal = b[key];

            if (key !== 'nama_kecamatan') {
                aVal = parseFloat(aVal) || 0;
                bVal = parseFloat(bVal) || 0;
            } else {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }

            if (aVal < bVal) return dir === 'asc' ? -1 : 1;
            if (aVal > bVal) return dir === 'asc' ? 1 : -1;
            return 0;
        });
    }

    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = filteredData.slice(start, end);
        const totalPages = Math.ceil(filteredData.length / perPage);

        if (pageData.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Data tidak ditemukan</p>
                    </td>
                </tr>
            `;
        } else {
            tableBody.innerHTML = pageData.map((item, idx) => {
                const net = parseFloat(item.net_carbon) || 0;
                const kredit = parseFloat(item.kredit_karbon) || 0;
                const status = item.status;

                let statusBadge = '';
                if (status === 'Defisit Karbon') {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Defisit</span>';
                } else if (status === 'Surplus Karbon') {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Surplus</span>';
                } else {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Seimbang</span>';
                }

                return `
                    <tr class="border-b border-gray-50 hover:bg-emerald-50/30 transition-colors duration-150">
                        <td class="px-5 py-3.5 text-gray-400 font-medium">${start + idx + 1}</td>
                        <td class="px-5 py-3.5 font-semibold text-gray-800">${item.nama_kecamatan}</td>
                        <td class="px-5 py-3.5 text-right text-red-600 font-medium">${parseFloat(item.emisi_co2).toLocaleString('id-ID', {minimumFractionDigits:2})}</td>
                        <td class="px-5 py-3.5 text-right text-emerald-600 font-medium">${parseFloat(item.absorpsi_co2).toLocaleString('id-ID', {minimumFractionDigits:2})}</td>
                        <td class="px-5 py-3.5 text-right font-bold ${net > 0 ? 'text-red-600' : 'text-emerald-600'}">${net.toLocaleString('id-ID', {minimumFractionDigits:2})}</td>
                        <td class="px-5 py-3.5 text-right font-bold text-purple-600">${kredit.toLocaleString('id-ID', {minimumFractionDigits:2})}</td>
                        <td class="px-5 py-3.5 text-center">${statusBadge}</td>
                    </tr>
                `;
            }).join('');
        }

        // Pagination info
        document.getElementById('paginationInfo').textContent =
            `Menampilkan ${start + 1}-${Math.min(end, filteredData.length)} dari ${filteredData.length} data`;

        // Pagination buttons
        const btnContainer = document.getElementById('paginationButtons');
        let btnHtml = '';

        if (totalPages > 1) {
            btnHtml += `<button onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 ${currentPage === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:bg-gray-50 cursor-pointer'} transition-colors">
                        ← Prev</button>`;

            for (let i = 1; i <= totalPages; i++) {
                btnHtml += `<button onclick="goToPage(${i})"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg border cursor-pointer transition-colors
                            ${i === currentPage ? 'bg-[#006954] text-white border-[#006954]' : 'border-gray-200 text-gray-600 hover:bg-gray-50'}">
                            ${i}</button>`;
            }

            btnHtml += `<button onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-200 ${currentPage === totalPages ? 'text-gray-300 cursor-not-allowed' : 'text-gray-600 hover:bg-gray-50 cursor-pointer'} transition-colors">
                        Next →</button>`;
        }

        btnContainer.innerHTML = btnHtml;
    }

    function updateSummary() {
        let defisit = 0, surplus = 0, seimbang = 0;
        allData.forEach(item => {
            if (item.status === 'Defisit Karbon') defisit++;
            else if (item.status === 'Surplus Karbon') surplus++;
            else seimbang++;
        });
        document.getElementById('defisitCount').textContent = defisit;
        document.getElementById('surplusCount').textContent = surplus;
        document.getElementById('seimbangCount').textContent = seimbang;
    }

    // Global pagination function
    window.goToPage = function (page) {
        const totalPages = Math.ceil(filteredData.length / perPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    };

    // Search
    searchInput.addEventListener('input', function () {
        applyFilters();
    });

    // Year filter
    yearFilter.addEventListener('change', function () {
        loadData();
    });

    // Column sorting
    document.querySelectorAll('[data-sort]').forEach(th => {
        th.addEventListener('click', function () {
            const key = this.dataset.sort;
            if (currentSort.key === key) {
                currentSort.dir = currentSort.dir === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.key = key;
                currentSort.dir = 'asc';
            }

            // Update icons
            document.querySelectorAll('.sort-icon').forEach(el => el.textContent = '↕');
            this.querySelector('.sort-icon').textContent = currentSort.dir === 'asc' ? '↑' : '↓';

            sortData();
            currentPage = 1;
            renderTable();
        });
    });

    // Initial load
    loadData();
});

// Export to Excel
function exportExcel() {
    const tahun = document.getElementById('yearFilter').value;

    fetch(`/api/carbon-trade?tahun=${tahun}&per_page=100`)
        .then(res => res.json())
        .then(json => {
            const data = json.data.data || [];
            const wsData = [
                ['No', 'Nama Kecamatan', 'Emisi CO₂ (ton)', 'Absorpsi CO₂ (ton)', 'Net Carbon (ton)', 'Kredit Karbon (ton)', 'Status']
            ];
            data.forEach((item, idx) => {
                wsData.push([
                    idx + 1,
                    item.nama_kecamatan,
                    parseFloat(item.emisi_co2),
                    parseFloat(item.absorpsi_co2),
                    parseFloat(item.net_carbon),
                    parseFloat(item.kredit_karbon),
                    item.status
                ]);
            });

            const ws = XLSX.utils.aoa_to_sheet(wsData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, `Carbon Trade ${tahun}`);
            XLSX.writeFile(wb, `Carbon_Trade_Bandar_Lampung_${tahun}.xlsx`);
        });
}

// Export to PDF
function exportPDF() {
    const tahun = document.getElementById('yearFilter').value;

    fetch(`/api/carbon-trade?tahun=${tahun}&per_page=100`)
        .then(res => res.json())
        .then(json => {
            const data = json.data.data || [];
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            doc.setFontSize(16);
            doc.text(`Carbon Trade - Kota Bandar Lampung (${tahun})`, 14, 20);
            doc.setFontSize(10);
            doc.text(`Digenerate pada: ${new Date().toLocaleDateString('id-ID')}`, 14, 28);

            const tableData = data.map((item, idx) => [
                idx + 1,
                item.nama_kecamatan,
                parseFloat(item.emisi_co2).toLocaleString('id-ID', {minimumFractionDigits:2}),
                parseFloat(item.absorpsi_co2).toLocaleString('id-ID', {minimumFractionDigits:2}),
                parseFloat(item.net_carbon).toLocaleString('id-ID', {minimumFractionDigits:2}),
                parseFloat(item.kredit_karbon).toLocaleString('id-ID', {minimumFractionDigits:2}),
                item.status
            ]);

            doc.autoTable({
                head: [['No', 'Kecamatan', 'Emisi CO₂', 'Absorpsi CO₂', 'Net Carbon', 'Kredit Karbon', 'Status']],
                body: tableData,
                startY: 35,
                styles: { fontSize: 8, cellPadding: 3 },
                headStyles: { fillColor: [0, 105, 84], textColor: [255, 255, 255] },
                alternateRowStyles: { fillColor: [245, 247, 250] },
            });

            doc.save(`Carbon_Trade_Bandar_Lampung_${tahun}.pdf`);
        });
}
</script>
@endpush
