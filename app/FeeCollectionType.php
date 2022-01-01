<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeCollectionType extends Model
{
    protected $table = 'feecollectiontype';
    public $timestamps = false;

    public function getAll($category = array())
    {
        return SELF::select('id', 'collectionhead', 'collectiondesc', 'br_id')->get()->toArray();
    }

    public function mapFeesCollectionType($data)
    {
        $temp = array();
        if (!empty($data)) {
           foreach ($data as $info) {
                $temp[$info['br_id']][$info['collectionhead']] = $info['id']; 
           } 
        }

        return $temp;
    }
}
