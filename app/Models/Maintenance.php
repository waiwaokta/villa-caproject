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
    protected $fillable = ['wismaID', 'date', 'reason'];
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

    public function wisma()
    {
        return $this->belongsTo(Wisma::class, 'wismaID', 'wismaID');
    }
}