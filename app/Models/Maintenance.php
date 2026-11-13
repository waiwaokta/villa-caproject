<?php
namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maintenance extends Model
{
    use HasFactory;
    protected $table = 'maintenance';
    protected $primaryKey = 'maintenanceID';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['villaID', 'date', 'reason'];
    protected $casts = [
        'date' => 'date',
    ];
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->maintenanceID)) {
                $model->maintenanceID = (string) Str::uuid();
            }
        });
    }

    public function villa()
    {
        return $this->belongsTo(Villa::class, 'villaID', 'villaID');
    }
}