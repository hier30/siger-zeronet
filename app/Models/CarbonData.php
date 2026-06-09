<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarbonData extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'carbon_data';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kecamatan_id',
        'tahun',
        'emisi_co2',
        'absorpsi_co2',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'emisi_co2' => 'decimal:2',
            'absorpsi_co2' => 'decimal:2',
            'tahun' => 'integer',
        ];
    }

    /**
     * Get the kecamatan that owns the carbon data.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the net carbon value.
     */
    public function getNetCarbonAttribute(): float
    {
        return (float) $this->emisi_co2 - (float) $this->absorpsi_co2;
    }

    /**
     * Get the kredit karbon value.
     */
    public function getKreditKarbonAttribute(): float
    {
        $net = $this->net_carbon;
        return $net > 0 ? $net : 0;
    }

    /**
     * Get the carbon status.
     */
    public function getStatusAttribute(): string
    {
        $net = $this->net_carbon;

        if ($net > 0) {
            return 'Defisit Karbon';
        } elseif ($net < 0) {
            return 'Surplus Karbon';
        }

        return 'Seimbang';
    }
}
