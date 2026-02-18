<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Administrator\Sbu;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass  assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'username',
        'password',
        'role_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_code', 'code');
    }

    /**
     * Check if user has access to a specific menu
     */
    public function hasMenuAccess($menuId, $permission = 'can_view')
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasMenuAccess($menuId, $permission);
    }

    /**
     * Get all accessible menu IDs for the user
     */
    public function getAccessibleMenuIds()
    {
        if (!$this->role) {
            return collect();
        }

        return RoleMenuAccess::where('role_code', $this->role_code)
            ->where('can_view', true)
            ->pluck('menu_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleCode)
    {
        return $this->role_code === $roleCode;
    }

    /**
     * Check if user is admin/super admin
     */
    public function isAdmin()
    {
        return in_array($this->role_code, ['ADMIN', 'SUPERADMIN']);
    }

    /**
     * Get user's OBS settings
     */
    public function obsSettings()
    {
        return $this->hasMany(UserObsSetting::class)->where('is_active', 1);
    }

    /**
     * Get user's default OBS setting
     */
    public function defaultObsSetting()
    {
        return $this->hasOne(UserObsSetting::class)->where('is_default', 1)->where('is_active', 1);
    }
}
