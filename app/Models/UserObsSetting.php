<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserObsSetting extends Model
{
    use SoftDeletes;

    protected $table = 'user_obs_settings';

    protected $fillable = [
        'user_id',
        'obs_name',
        'obs_url',
        'obs_password',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
