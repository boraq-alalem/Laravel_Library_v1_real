<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $table = 'user_activity_log';

    protected $fillable = [
        'super_admin_id',
        'user_id',
        'action',
        'old_role',
        'new_role',
        'details', // Add this line to allow mass assignment for the 'details' column
    ];

    public function superAdmin()
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
