<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class population extends Model
{
    protected $fillable = [
        'year',
        'population',
        'municipio_id',
        'gender',
        'age'
    ];

    public function municipio()
    {
        return $this->belongsTo(municipio::class);
    }
}
