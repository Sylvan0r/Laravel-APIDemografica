<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class municipio extends Model
{
    protected $fillable = [
        'name',
        'isla_id'
    ];

    public function isla()
    {
        return $this->belongsTo(isla::class);
    }
}