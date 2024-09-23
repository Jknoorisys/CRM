<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroups extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'user_groups';

    protected $fillable = [
        'name',
        'login_access',
        'contact_permissions',
        'lead_permissions',
        'activity_permissions',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_group_id')->withTrashed();
    }

}
