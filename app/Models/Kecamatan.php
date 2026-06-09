<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'kecamatan';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'kecamatan',
        'geom',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'geojson' => 'array',
        ];
    }

    /**
     * Get the carbon data records for the kecamatan.
     */
    public function carbonData(): HasMany
    {
        return $this->hasMany(CarbonData::class);
    }
}
