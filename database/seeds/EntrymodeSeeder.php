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
                'entry_modename' => 'revdue',
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
                'entry_modename' => 'rcpt',
                'crdr' => 'C',
                'entrymodeno' => '0',
            ],
            [
                'entry_modename' => 'revrcpt',
                'crdr' => 'D',
                'entrymodeno' => '0',
            ],
            [
                'entry_modename' => 'jv',
                'crdr' => 'C',
                'entrymodeno' => '14',
            ],
            [
                'entry_modename' => 'revjv',
                'crdr' => 'D',
                'entrymodeno' => '14',
            ],
            [
                'entry_modename' => 'pmt',
                'crdr' => 'D',
                'entrymodeno' => '1',
            ],
            [
                'entry_modename' => 'revpmt',
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
