<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VillaPhoto extends Model
{
    use HasFactory;

    protected $table = 'villa_photos';
    protected $primaryKey = 'photoID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'villaID', 'file_path', 'is_primary', 'order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->photoID)) {
                $model->photoID = (string) Str::uuid();
            }
        });
    }

    public function villa()
    {
        return $this->belongsTo(Villa::class, 'villaID', 'villaID');
    }
}