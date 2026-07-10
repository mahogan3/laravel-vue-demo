<?php

namespace App\Console\Commands;

use App\Models\AuthUser;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('auth:promote {email : Email of an existing (already signed up) user}')]
#[Description('Grant a signed-up user the admin role')]
class PromoteAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = AuthUser::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error("No user found with email {$this->argument('email')}. They need to sign up first.");

            return self::FAILURE;
        }

        $user->update(['role' => 'admin']);

        $this->info("{$user->email} is now an admin. They'll need to sign out and back in to pick up the new role.");

        return self::SUCCESS;
    }
}
