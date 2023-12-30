<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'leads';
    protected $fillable = [
        'contact',
        'title',
        'description',
        'stage',
        'source',
        'type',
        'assigned_to',
        'created_by',
        'last_contacted_date'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->incrementing = false; 

            $lastRecord = self::latest()->first();
            $nextNumber = $lastRecord ? substr($lastRecord->id, 2) + 1 : 1;
            $model->id = 'NS' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        });
    }

    public function contact() : BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact')->withTrashed();
    }

    public function stage() : BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage')->withTrashed();
    }

    public function source() : BelongsTo
    {
        return $this->belongsTo(Source::class, 'source')->withTrashed();
    }

    public function type() : BelongsTo
    {
        return $this->belongsTo(LeadType::class, 'type')->withTrashed();
    }

    public function assignedTo() : BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withTrashed();
    }

    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'lead_id', 'id')->withTrashed();
    }
}
