<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Villa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'villas';
    protected $primaryKey = 'villaID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'desc', 'location', 'address', 'capacity', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->villaID)) {
                $model->villaID = (string) Str::uuid();
            }
        });
    }

    public function villaPhotos()
    {
        return $this->hasMany(VillaPhoto::class, 'villaID', 'villaID')
                    ->orderBy('order', 'asc');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'villaID', 'villaID')
                    ->orderByRaw("FIELD(day_type, 'weekday', 'weekend', 'holiday')"); // urutan hari
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'villaID', 'villaID');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(VillaPhoto::class, 'villaID', 'villaID')
                    ->where('is_primary', true)
                    ->orderBy('order', 'asc');
    }
    public function facilities()
    {
        return $this->belongsToMany(
            Facility::class,
            'villa_facility',
            'villaID',
            'facilityID'
        );
    }

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class, 'villaID', 'villaID');
    }
}