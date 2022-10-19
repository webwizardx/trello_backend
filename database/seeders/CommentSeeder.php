<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrFail();
        $todo = Todo::firstOrFail();
        Comment::factory()->count(3)->for($user)->for($todo)->create();
    }
}
