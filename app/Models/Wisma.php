<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wisma extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'wismaID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'desc', 'location', 'capacity', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->wismaID)) {
                $model->wismaID = (string) Str::uuid();
            }
        });
    }

    public function photos()
    {
        return $this->hasMany(WismaPhoto::class, 'wismaID', 'wismaID');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'wismaID', 'wismaID');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'wismaID', 'wismaID');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(WismaPhoto::class, 'wismaID', 'wismaID')
                    ->where('is_primary', true);
    }
}