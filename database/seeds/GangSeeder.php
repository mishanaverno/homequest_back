<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('gang')->insert([
            'name' =>  'Home'
        ]);
    }
}
