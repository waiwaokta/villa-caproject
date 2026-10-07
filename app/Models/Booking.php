<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookings';
    protected $primaryKey = 'bookingID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'villaID', 'check_in', 'check_out',
        'total_nights', 'total_price',
        'guest_name', 'guest_phone',
        'status', 'reject_desc',
        'expires_at', 'paid_at',
        'payment_order_id', 'qris_url'
    ];

    protected $casts = [
        'check_in'    => 'date',
        'check_out'   => 'date',
        'total_price' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at'    => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->bookingID)) {
                // Format BK-YYYY-XXXXXX
                $model->bookingID = 'BK-' . date('Y') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function villa()
    {
        return $this->belongsTo(Villa::class, 'villaID', 'villaID');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'bookingID', 'bookingID');
    }

    public function scopeLocking($q)
    {
        return $q->where(fn ($w) => $w->where('status', 'approved')
            ->orWhere(fn ($p) => $p->where('status', 'pending')
                ->where(fn ($x) => $x->whereNull('expires_at')->orWhere('expires_at', '>', now()))));
    }
}