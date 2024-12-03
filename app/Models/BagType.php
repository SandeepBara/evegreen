<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BagType extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bag_type',
        "formula_to_bag_by_length",
        "formula_to_bag_by_weight",
        'lock_status',
    ];

    public function store($request){        
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

    public function getBagListOrm(){
        return self::where("lock_status",false);
    }
}
