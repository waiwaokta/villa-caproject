<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'bookingID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'wismaID', 'check_in', 'check_out',
        'total_nights', 'total_price', 'user_type', 'booking_type',
        'guest_name', 'guest_phone', 'guest_ktp',
        'employee_id', 'inst_name', 'inst_npwp',
        'status', 'reject_desc'
    ];

    protected $casts = [
        'check_in'    => 'date',
        'check_out'   => 'date',
        'total_price' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->bookingID)) {
                // Format WPL-YYYY-XXXXXX
                $model->bookingID = 'WPL-' . date('Y') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function wisma()
    {
        return $this->belongsTo(Wisma::class, 'wismaID', 'wismaID');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'bookingID', 'bookingID');
    }
}