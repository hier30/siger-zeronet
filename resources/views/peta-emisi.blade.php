@extends('layouts.app')

@section('title', 'Peta Emisi CO₂')
@section('page-title', 'Peta Emisi CO₂')
@section('page-subtitle', 'Visualisasi choropleth emisi karbon per kecamatan')

@section('year-filter')
<form method="GET" action="{{ route('peta-emisi') }}" class="flex items-center gap-2">
    <label class="text-xs font-medium text-gray-500">Tahun:</label>
    <select name="tahun" onchange="this.form.submit()"
            class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none cursor-pointer">
        @foreach($availableYears as $year)
            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
        @endforeach
    </select>
</form>
@endsection

@push('styles')
<style>
    #map-emisi {
        height: calc(100vh - 160px);
        width: 100%;
        border-radius: 16px;
        z-index: 1;
    }
    .info-panel {
        padding: 10px 14px;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        line-height: 1.6;
        max-width: 220px;
    }
    .info-panel h4 {
        margin: 0 0 6px 0;
        font-weight: 700;
        font-size: 13px;
        color: #1F2937;
    }
    .legend-panel {
        padding: 10px 14px;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        font-family: 'Inter', sans-serif;
        font-size: 11px;
        line-height: 1.8;
    }
    .legend-panel h4 {
        margin: 0 0 6px 0;
        font-weight: 700;
        font-size: 12px;
        color: #1F2937;
    }
    .legend-panel i {
        width: 16px;
        height: 16px;
        display: inline-block;
        margin-right: 6px;
        border-radius: 3px;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div id="map-emisi"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map-emisi', {
        zoomControl: false
    }).setView([-5.4, 105.26], 12);

    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    // Color scale for emission choropleth
    function getEmisiColor(val) {
        return val > 3500 ? '#004236' :
               val > 2500 ? '#006954' :
               val > 1500 ? '#65A30D' :
                            '#D9F99D';
    }

    function getEmisiLabel(val) {
        return val > 3500 ? 'Sangat Tinggi' :
               val > 2500 ? 'Tinggi' :
               val > 1500 ? 'Sedang' :
                            'Rendah';
    }

    let geojsonLayer;
    let infoControl;

    // Info control (hover)
    infoControl = L.control({ position: 'topleft' });
    infoControl.onAdd = function () {
        this._div = L.DomUtil.create('div', 'info-panel');
        this.update();
        return this._div;
    };
    infoControl.update = function (props) {
        if (props) {
            const net = props.net_carbon;
            const statusColor = net > 0 ? '#EF4444' : net < 0 ? '#10B981' : '#F59E0B';
            this._div.innerHTML = `
                <h4>${props.nama_kecamatan}</h4>
                <div style="color:#6B7280;">
                    <b style="color:#EF4444;">Emisi:</b> ${parseFloat(props.emisi_co2).toLocaleString('id-ID')} ton<br>
                    <b style="color:#10B981;">Absorpsi:</b> ${parseFloat(props.absorpsi_co2).toLocaleString('id-ID')} ton<br>
                    <b>Net Carbon:</b> ${parseFloat(net).toLocaleString('id-ID')} ton<br>
                    <b>Status:</b> <span style="color:${statusColor}; font-weight:600;">${props.status}</span>
                </div>
            `;
        } else {
            this._div.innerHTML = '<h4>Peta Emisi CO₂</h4><span style="color:#9CA3AF;">Hover pada kecamatan</span>';
        }
    };
    infoControl.addTo(map);

    // Legend
    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function () {
        const div = L.DomUtil.create('div', 'legend-panel');
        const grades = [0, 1500, 2500, 3500];
        const labels = ['Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'];
        let html = '<h4>Emisi CO₂ (ton)</h4>';
        for (let i = 0; i < grades.length; i++) {
            html += `<i style="background:${getEmisiColor(grades[i] + 1)}"></i> ${labels[i]}<br>`;
        }
        div.innerHTML = html;
        return div;
    };
    legend.addTo(map);

    // Fetch GeoJSON data
    fetch(`/api/peta-emisi?tahun={{ $tahun }}`)
        .then(res => res.json())
        .then(data => {
            geojsonLayer = L.geoJSON(data, {
                style: function (feature) {
                    return {
                        fillColor: getEmisiColor(feature.properties.emisi_co2),
                        weight: 2,
                        opacity: 1,
                        color: '#FFFFFF',
                        fillOpacity: 0.75
                    };
                },
                onEachFeature: function (feature, layer) {
                    // Hover
                    layer.on({
                        mouseover: function (e) {
                            const l = e.target;
                            l.setStyle({ weight: 3, color: '#006954', fillOpacity: 0.9 });
                            l.bringToFront();
                            infoControl.update(feature.properties);
                        },
                        mouseout: function (e) {
                            geojsonLayer.resetStyle(e.target);
                            infoControl.update();
                        },
                        click: function (e) {
                            map.fitBounds(e.target.getBounds(), { padding: [50, 50] });
                        }
                    });

                    // Popup
                    const p = feature.properties;
                    const net = p.net_carbon;
                    const statusColor = net > 0 ? '#EF4444' : net < 0 ? '#10B981' : '#F59E0B';
                    layer.bindPopup(`
                        <div style="font-family:Inter,sans-serif; font-size:12px; line-height:1.8; min-width:180px;">
                            <div style="font-weight:700; font-size:14px; color:#1F2937; margin-bottom:6px; border-bottom:2px solid #006954; padding-bottom:4px;">
                                ${p.nama_kecamatan}
                            </div>
                            <b style="color:#EF4444;">Emisi CO₂:</b> ${parseFloat(p.emisi_co2).toLocaleString('id-ID')} ton<br>
                            <b style="color:#10B981;">Absorpsi CO₂:</b> ${parseFloat(p.absorpsi_co2).toLocaleString('id-ID')} ton<br>
                            <b>Net Carbon:</b> ${parseFloat(net).toLocaleString('id-ID')} ton<br>
                            <span style="display:inline-block; margin-top:4px; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:600; color:white; background:${statusColor};">
                                ${p.status}
                            </span>
                        </div>
                    `, { maxWidth: 250 });
                }
            }).addTo(map);

            // Fit bounds
            if (geojsonLayer.getBounds().isValid()) {
                map.fitBounds(geojsonLayer.getBounds(), { padding: [30, 30] });
            }
        })
        .catch(err => console.error('Error loading GeoJSON:', err));
});
</script>
@endpush
