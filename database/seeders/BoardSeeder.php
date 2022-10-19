<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrFail();
        $workspace = Workspace::firstOrFail();
        Board::factory()->hasAttached($user)->for($workspace)->create();
    }
}
