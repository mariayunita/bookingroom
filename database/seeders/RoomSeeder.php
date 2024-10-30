<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Room::create(['name' => 'Room A']);
        Room::create(['name' => 'Room B']);
        Room::create(['name' => 'Room C']);
    }
}
