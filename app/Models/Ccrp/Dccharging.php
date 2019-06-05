<?php

namespace App\Models\Ccrp;

use Illuminate\Database\Eloquent\Model;
use stdClass;

class Dccharging extends Model
{
    protected $connection = 'dbcaiji';
    protected $table = 'dccharging';
    protected $primaryKey = 'data_id';

    public function checkVolt($sender_id = null)
    {
        if ($sender_id === null) {
            $query = self::where(['sender_volt' => 0])
                ->where('sender_trans_time', '>', time() - 24 * 3600)
                ->select(\DB::raw('"sender_id" as object_key'), \DB::raw('sender_id as object_value'), \DB::raw('count(1) as result'))
                ->groupBy('sender_id')
                ->havingRaw('count(1) > ?', [5])
                ->get();
            return $query;
        } else {
            $query = self::where(['sender_volt' => 0])
                ->where('sender_id', $sender_id)
                ->where('sender_trans_time', '>', time() - 24 * 3600)
                ->select(\DB::raw('"sender_id" as object_key'), \DB::raw('sender_id as object_value'), \DB::raw('count(1) as result'))
                ->count();

            $result = new StdClass();
            $result->object_key = 'sender_id';
            $result->object_value = $sender_id;
            $result->result = $query;
            return $result;

        }
    }
}
