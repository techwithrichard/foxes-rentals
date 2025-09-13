<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    protected $signature = 'users:list';
    protected $description = 'List all users with their roles';

    public function handle()
    {
        $this->info('Available Users:');
        $this->line('');
        
        $users = User::with('roles')->get(['id', 'name', 'email', 'phone']);
        
        $this->table(['ID', 'Name', 'Email', 'Phone', 'Roles'], 
            $users->map(function($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->roles->pluck('name')->join(', ')
                ];
            })->toArray()
        );
        
        return 0;
    }
}
