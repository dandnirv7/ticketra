<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
  public function run(): void
  {
    User::updateOrCreate(
      ['email' => 'admin@ticketra.web.id'],
      [
        'name' => 'Admin Ticketra',
        'password' => Hash::make('password123'),
      ]
    );

    $this->command->info('Admin user created: admin@ticketra.web.id / password123');
  }
}
