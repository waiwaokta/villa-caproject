<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities';
    protected $primaryKey = 'facilityID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'icon'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->facilityID)) {
                $model->facilityID = (string) Str::uuid();
            }
        });
    }

    public function villas()
    {
        return $this->belongsToMany(
            Villa::class,
            'villa_facility',
            'facilityID',
            'villaID'
        );
    }
}