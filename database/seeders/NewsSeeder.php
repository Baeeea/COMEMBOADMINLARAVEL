<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('news')->insert([
            [
                'title' => 'Community Event Announcement',
                'content' => 'Join us for the upcoming community gathering this weekend.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'New Barangay Initiatives',
                'content' => 'Learn about the new programs being launched in our barangay.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'System Maintenance Notice',
                'content' => 'The system will undergo maintenance on Sunday from 2-4 AM.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
