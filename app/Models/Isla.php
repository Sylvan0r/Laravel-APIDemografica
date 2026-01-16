<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class isla extends Model
{
    protected $fillable = [
        'name'
    ];

    public function municipios()
    {
        return $this->hasMany(municipio::class);
    }
}