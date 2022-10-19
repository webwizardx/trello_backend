<?php

namespace Database\Seeders;

use App\Models\Lists;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrFail();
        $lists = Lists::firstOrFail();
        Todo::factory()->count(3)->hasAttached($user)->for($lists, 'list')->create();
    }
}
