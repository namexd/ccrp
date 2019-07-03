<?php
namespace App\Models\Ccrp;
use Illuminate\Database\Eloquent\Model;

class Collectorguanxi extends Model{

    protected $table = 'guanxi';
    protected $connection = 'dbqingxi'; //DB_CAIJI

    protected $fillable=[
        'sensor_id','biao_id','supplier_id','create_time'
    ];

    public $timestamps=false;
    public function create_pg2w($sensor_id)
    {
        $sql2="select create_sensortable('".$sensor_id."') as result;";
        $rs2=\DB::connection('dbhistory')->select($sql2);
//        $rs2= M($table_name,' ','DB_PGSQL')->query($sql2);
        return $rs2;
    }
    public function get_pg2w()
    {
        $sql="select tablename from pg_tables where schemaname='sensor';";
        $rs2=\DB::connection('dbhistory')->select($sql);
//        $rs2= M('pg_tables',' ','DB_PGSQL')->query($sql);
        return $rs2;
    }
    public function addnew($sensor_id,$supplier_id= 1001)
    {
        $sensor_id = trim(str_replace('	','',$sensor_id));
        $data = array('sensor_id'=>$sensor_id);
        $find = $this->where($data)->first();
        if(!$find){
            //创建PG
            $this->create_pg2w($sensor_id);
            //创建mysql
            $data['biao_id'] = create_biao_id($sensor_id,$supplier_id);
            $data['supplier_id']= $supplier_id;
            $data['create_time']= time();
            $add = $this->create($data);


            Collector::where('supplier_collector_id',$sensor_id)->update(['pgsql_date'=>time()]);


            return $add;
        }else{
            if($find['supplier_id']<>$supplier_id)
                $this->where($data)->update(['supplier_id'=>$supplier_id]);
        }

    }

}
function create_biao_id($sensor_id, $supplier_id = 1001)

{

    return $sensor_id % 100;

}
