<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recording extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'user_id',
        'filename',
        'file_path',
        'custom_filename',
        'status',
        'started_at',
        'stopped_at',
        'duration',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'recording' => '<span class="badge badge-danger">🔴 Recording</span>',
            'stopped' => '<span class="badge badge-warning">⏸️ Stopped</span>',
            'completed' => '<span class="badge badge-success">✓ Completed</span>',
            'failed' => '<span class="badge badge-dark">✗ Failed</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }
}
