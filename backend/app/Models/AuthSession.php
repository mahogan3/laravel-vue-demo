<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Read-only view of the `session` table that better-auth (the Node auth
 * server) owns and writes to. Laravel never creates or updates rows here.
 */
class AuthSession extends Model
{
    protected $table = 'session';

    public $timestamps = false;

    // better-auth generates non-incrementing string (cuid) primary keys.
    // Without this, Eloquent silently mangles the id during relation eager-loading.
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'expiresAt' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(AuthUser::class, 'userId');
    }
}
