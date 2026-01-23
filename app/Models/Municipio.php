<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class municipio extends Model
{
    protected $table = 'municipio'; 
    protected $fillable = [
        'name',
        'isla_id'
    ];

    public function isla()
    {
        return $this->belongsTo(isla::class);
    }

    public function getRouteKeyName()
    {
        return 'gdc_municipio';
    }

    public function population()
    {
        return $this->hasMany(population::class, 'gdc_municipio', 'gdc_municipio');
    }
}