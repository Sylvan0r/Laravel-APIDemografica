<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class population extends Model
{
    protected $table = 'population'; 
    protected $fillable = [
        'year',
        'population',
        'gdc_municipio',
        'gender',
        'age'
    ];

    public function municipio()
    {
        return $this->belongsTo(municipio::class, 'gdc_municipio', 'gdc_municipio');
    }
}
