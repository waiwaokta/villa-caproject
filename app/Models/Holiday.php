<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'holidays';
    protected $primaryKey = 'holidayID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['date', 'name'];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->holidayID)) {
                $model->holidayID = (string) Str::uuid();
            }
        });
    }
}