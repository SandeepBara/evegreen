<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class MenuMaster extends Model
{
    use Loggable;
    //
    protected $fillable = ['menu_name',"order_no",'parent_menu_mstr_id','url_path','query_string','menu_icon','menu_type','lock_status'];

    public function getUserTypeList(){
        return $this->hasManyThrough(UserTypeMaster::class,MenuPermission::class,"menu_master_id","id","id","user_type_master_id")->where("menu_permissions.lock_status",false);
    }

    public function store($request){
        $test = self::where(DB::raw("upper(url_path)"),Str::upper($request->url_path))
                ->where(DB::raw("menu_name"),($request->menu_name))
                ->first();
        if($test){
            $newRequest = new Request(["id"=>$test->id,"lockStatus"=>false]);
            $this->edit($newRequest);
            return $test->id;
        }
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function edit($request){
        $inputs = snakeCase($request)->filter(function($val,$index){
            return (in_array($index,$this->fillable));
        });
        $return= self::where("id",$request->id)->update($inputs->all());
        return $return;
    }

    public function subMenuOrm(){
        return self::where("menu_type",1)->where("lock_status",false);
    }
}
