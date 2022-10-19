<?php

namespace Database\Seeders;


use App\Models\Board;
use App\Models\Lists;
use Illuminate\Database\Seeder;

class ListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $board = Board::firstOrFail();
        Lists::factory()->count(3)->for($board)->create();
    }
}
