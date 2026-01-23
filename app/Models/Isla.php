<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class isla extends Model
{
    protected $table = 'isla'; 
    protected $fillable = [
        'name'
    ];

    public function municipios()
    {
        return $this->hasMany(municipio::class);
    }

    public function getRouteKeyName()
    {
        return 'gdc_isla';
    }
}