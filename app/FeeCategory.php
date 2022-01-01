<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    protected $table = 'feecategory';
    public $timestamps = false;

    public function getAll($category = array())
    {
        if (!empty($category)) {
            return SELF::select('id', 'fee_category', 'br_id')->whereIn('fee_category', $category)->get()->toArray();
        }

        return SELF::select('id', 'fee_category', 'br_id')->get()->toArray();
    }

    public function mapFeesCategory($data)
    {
        $temp = array();
        if (!empty($data)) {
            foreach ($data as $info) {
                $temp[$info['br_id']][$info['fee_category']] = $info['id'];
            }
        }

        return $temp;
    }
}
