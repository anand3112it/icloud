<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeTypes extends Model
{
    protected $table = 'fee_types';
    public $timestamps = false;

    public function getAll($types)
    {
        if (!empty($types)) {
            return SELF::select('id', 'f_name', 'br_id')->whereIn('f_name', $types)->get()->toArray();
        }

        return SELF::select('id', 'f_name', 'br_id')->get()->toArray();
    }

    public function mapFeesType($data)
    {
        $temp = array();
        if (!empty($data)) {
           foreach ($data as $info) {
                $temp[$info['br_id']][$info['f_name']] = $info['id']; 
           } 
        }

        return $temp;
    }
}
