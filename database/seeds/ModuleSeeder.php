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
                'module_name' => 'Academic',
                'module_id' => '1',
            ],
            [
                'module_name' => 'AcademicMisc',
                'module_id' => '11',
            ],
            [
                'module_name' => 'Hostel',
                'module_id' => '2',
            ],
            [
                'module_name' => 'HostelMisc',
                'module_id' => '22',
            ],
            [
                'module_name' => 'Transport',
                'module_id' => '3',
            ],
            [
                'module_name' => 'TransportMisc',
                'module_id' => '33',
            ],
        ]);
    }
}
