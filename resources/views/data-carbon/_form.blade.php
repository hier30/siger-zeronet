@csrf

@foreach($returnQuery ?? [] as $key => $value)
    <input type="hidden" name="return_{{ $key }}" value="{{ $value }}">
@endforeach

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="md:col-span-2">
        <label for="kecamatan_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Kecamatan</label>
        <select id="kecamatan_id" name="kecamatan_id" required
                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm bg-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
            <option value="">Pilih kecamatan</option>
            @foreach($kecamatan as $item)
                <option value="{{ $item->id }}" @selected(old('kecamatan_id', $carbonData->kecamatan_id) == $item->id)>
                    {{ $item->kecamatan }}
                </option>
            @endforeach
        </select>
        @error('kecamatan_id')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun</label>
        <input id="tahun" name="tahun" type="number" inputmode="numeric" value="{{ old('tahun', $carbonData->tahun) }}" required
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('tahun')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="emisi_co2" class="block text-sm font-semibold text-gray-700 mb-1.5">Emisi CO2</label>
        <input id="emisi_co2" name="emisi_co2" type="number" step="0.01" value="{{ old('emisi_co2', $carbonData->emisi_co2) }}" required
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('emisi_co2')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="absorpsi_co2" class="block text-sm font-semibold text-gray-700 mb-1.5">Absorpsi CO2</label>
        <input id="absorpsi_co2" name="absorpsi_co2" type="number" step="0.01" value="{{ old('absorpsi_co2', $carbonData->absorpsi_co2) }}" required
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('absorpsi_co2')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
    <a href="{{ route('data-carbon.index', $returnQuery ?? []) }}"
       class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
        Batal
    </a>
    <button type="submit"
            class="rounded-lg bg-[#006954] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#004236]">
        Simpan
    </button>
</div>
