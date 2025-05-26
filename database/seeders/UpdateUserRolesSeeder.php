<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update the first user to be an admin
        DB::table('users')
            ->where('id', 1)
            ->update(['role' => 'admin']);
    }
}
