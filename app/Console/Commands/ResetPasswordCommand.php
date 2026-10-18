<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:reset-password {email=admin@example.com} {password=admin123}';

    /**
     * The command aliases.
     *
     * @var array<int, string>
     */
    protected $aliases = ['user:reset-password', 'reset:password'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset a user password';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => 'System Administrator',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
            ]);
            $this->info("User {$email} created with password: {$password}");

            return Command::SUCCESS;
        }

        $user->password = Hash::make($password);
        $user->is_active = true;
        $user->save();

        $this->info("Password for user {$email} reset to: {$password}");

        // Also test check
        $check = Hash::check($password, $user->password);
        $this->info('Password verification check: '.($check ? 'PASS' : 'FAIL'));

        return Command::SUCCESS;
    }
}
