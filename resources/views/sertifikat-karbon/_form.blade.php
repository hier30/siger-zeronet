@csrf

@foreach($returnQuery ?? [] as $key => $value)
    <input type="hidden" name="return_{{ $key }}" value="{{ $value }}">
@endforeach

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label for="nama_pemilik" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Pemilik</label>
        <input id="nama_pemilik" name="nama_pemilik" type="text" value="{{ old('nama_pemilik', $sertifikatKarbon->nama_pemilik) }}" required
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('nama_pemilik')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="wilayah" class="block text-sm font-semibold text-gray-700 mb-1.5">Wilayah</label>
        <input id="wilayah" name="wilayah" type="text" value="{{ old('wilayah', $sertifikatKarbon->wilayah) }}" required
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('wilayah')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="luas_lahan" class="block text-sm font-semibold text-gray-700 mb-1.5">Luas Lahan</label>
        <input id="luas_lahan" name="luas_lahan" type="number" step="0.01" value="{{ old('luas_lahan', $sertifikatKarbon->luas_lahan) }}" required
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('luas_lahan')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="jumlah_serapan" class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah Serapan</label>
        <input id="jumlah_serapan" name="jumlah_serapan" type="number" step="0.01" value="{{ old('jumlah_serapan', $sertifikatKarbon->jumlah_serapan) }}" required
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('jumlah_serapan')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="satuan" class="block text-sm font-semibold text-gray-700 mb-1.5">Satuan</label>
        <input id="satuan" name="satuan" type="text" value="{{ old('satuan', $sertifikatKarbon->satuan ?? 'ton CO2e') }}"
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('satuan')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun</label>
        <input id="tahun" name="tahun" type="number" inputmode="numeric" value="{{ old('tahun', $sertifikatKarbon->tahun) }}" required
               class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
        @error('tahun')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
        <select id="status" name="status" required
                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm bg-white outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">
            @foreach($statuses as $statusOption)
                <option value="{{ $statusOption }}" @selected(old('status', $sertifikatKarbon->status) === $statusOption)>
                    {{ $statusOption }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="4"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20">{{ old('keterangan', $sertifikatKarbon->keterangan) }}</textarea>
        @error('keterangan')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
    <a href="{{ route('sertifikat-karbon.index', $returnQuery ?? []) }}"
       class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
        Batal
    </a>
    <button type="submit"
            class="rounded-lg bg-[#006954] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#004236]">
        Simpan
    </button>
</div>
