<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadSource extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'lead_sources';

    protected $fillable = [
        'source',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function contacts() : HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
