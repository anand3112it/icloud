<?php

use Illuminate\Database\Seeder;

class EntrymodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('entrymode')->insert([
            [
                'entry_modename' => 'due',
                'crdr' => 'D',
                'entrymodeno' => '0',
            ],
            [
                'entry_modename' => 'REVDUE',
                'crdr' => 'C',
                'entrymodeno' => '12',
            ],
            [
                'entry_modename' => 'scholarship',
                'crdr' => 'C',
                'entrymodeno' => '15',
            ],
            [
                'entry_modename' => 'scholarshipprev/reconsession',
                'crdr' => 'D',
                'entrymodeno' => '16',
            ],
            [
                'entry_modename' => 'consession',
                'crdr' => 'C',
                'entrymodeno' => '15',
            ],
            [
                'entry_modename' => 'RCPT',
                'crdr' => 'C',
                'entrymodeno' => '0',
            ],
            [
                'entry_modename' => 'REVRCPT',
                'crdr' => 'D',
                'entrymodeno' => '0',
            ],
            [
                'entry_modename' => 'JV',
                'crdr' => 'C',
                'entrymodeno' => '14',
            ],
            [
                'entry_modename' => 'RevJV',
                'crdr' => 'D',
                'entrymodeno' => '14',
            ],
            [
                'entry_modename' => 'PMT',
                'crdr' => 'D',
                'entrymodeno' => '1',
            ],
            [
                'entry_modename' => 'REVPMT',
                'crdr' => 'C',
                'entrymodeno' => '1',
            ],
            [
                'entry_modename' => 'fundtransfer',
                'crdr' => 'CD',
                'entrymodeno' => '1',
            ],
        ]);
    }
}
