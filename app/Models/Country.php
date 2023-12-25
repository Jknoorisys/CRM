<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'countries';

    protected $fillable = [
        'country',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function cities() : HasMany
    {
        return $this->hasMany(City::class);
    }

    public function contacts() : HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
