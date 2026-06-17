<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatKarbon extends Model
{
    protected $fillable = [
        'nama_pemilik',
        'wilayah',
        'luas_lahan',
        'jumlah_serapan',
        'satuan',
        'tahun',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'luas_lahan' => 'decimal:2',
            'jumlah_serapan' => 'decimal:2',
            'tahun' => 'integer',
        ];
    }
}
