<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * View of the `user` table that better-auth (the Node auth server) owns.
 * Laravel only ever writes to `role`, via the `auth:promote` command.
 */
class AuthUser extends Model
{
    protected $table = 'user';

    public $timestamps = false;

    // better-auth generates non-incrementing string (cuid) primary keys.
    // Without this, Eloquent silently mangles the id during relation eager-loading.
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['role'];

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
