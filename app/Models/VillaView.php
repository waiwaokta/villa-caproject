<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillaView extends Model
{
    public $timestamps = false;

    protected $fillable = ['villaID', 'visitor_hash', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];
}