<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserUuid extends Model
{
    protected $table = 'user_uuids';
    protected $fillable = [
        'id_local',
        'id_remote',
    ];
}
