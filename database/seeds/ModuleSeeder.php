<?php

use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module')->insert([
            [
                'module_name' => 'academic',
                'module_id' => '1',
            ],
            [
                'module_name' => 'academicmisc',
                'module_id' => '11',
            ],
            [
                'module_name' => 'hostel',
                'module_id' => '2',
            ],
            [
                'module_name' => 'hostelmisc',
                'module_id' => '22',
            ],
            [
                'module_name' => 'transport',
                'module_id' => '3',
            ],
            [
                'module_name' => 'transportmisc',
                'module_id' => '33',
            ],
        ]);
    }
}
