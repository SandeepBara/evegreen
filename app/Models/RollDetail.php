<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RollDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'roll_no',
        'purchase_date',
        'vendor_id',
        'roll_size',
        'roll_gsm',
        'roll_color',
        'roll_length',
        'net_weight',
        'gross_weight',
        'W',
        'L',
        'G',
        'for_client_id',
        'printing_description',
        'bag_type_id',
        'bag_units',
        'is_printed',
        'printing_date',
        'weight_after_printing',
        'is_roll_cut',
        'cutting_date',
        'lock_status',
    ];

    public function getPrintingColor(){
        return $this->hasMany(RollPrintColor::class,"roll_id","id")->where("lock_status",false);
    }

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
