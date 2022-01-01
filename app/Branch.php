<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branch';
    public $timestamps = false;

    public function getAll($branch = array())
    {
        if (!empty($branch)) {
            return SELF::select('id', 'name')->whereIn('name', $branch)->get()->toArray();
        }

        return SELF::select('id', 'name')->get()->toArray();
    }
}
