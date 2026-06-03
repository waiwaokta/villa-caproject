<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory;

    protected $primaryKey = 'priceID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'wismaID', 'user_type', 'day_type', 'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->priceID)) {
                $model->priceID = (string) Str::uuid();
            }
        });
    }

    public function wisma()
    {
        return $this->belongsTo(Wisma::class, 'wismaID', 'wismaID');
    }
}