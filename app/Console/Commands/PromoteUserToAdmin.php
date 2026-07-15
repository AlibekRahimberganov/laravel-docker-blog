<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:promote {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote an existing user to the admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error("User with email '{$this->argument('email')}' not found.");
            return self::FAILURE;
        }

        $user->forceFill(['role' => 'admin'])->save();

        $this->info("{$user->email} is now an admin.");
        return self::SUCCESS;
    }
}
