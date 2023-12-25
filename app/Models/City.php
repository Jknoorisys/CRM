<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cities';
    
    protected $fillable = [
        'country_id',
        'city',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id')->withTrashed();
    }

    public function contacts() : HasMany
    {
        return $this->hasMany(Contact::class)->withTrashed();
    }
}
