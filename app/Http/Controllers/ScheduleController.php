<?php

namespace App\Http\Controllers;

use App\Models\CuttingScheduleDetail;
use App\Models\MachineMater;
use App\Models\PrintingScheduleDetail;
use App\Models\RollDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    //
    protected $_M_RollDetail;
    protected $_M_Machine;
    protected $_M_PrintingScheduleDetail;
    protected $_M_CuttingScheduleDetail;

    function __construct()
    {
        $this->_M_RollDetail = new RollDetail();
        $this->_M_Machine = new MachineMater();
        $this->_M_PrintingScheduleDetail = new PrintingScheduleDetail();
        $this->_M_CuttingScheduleDetail = new CuttingScheduleDetail();
    }
    public function getRollForPrinting(Request $request){
        try{

            $flag= $request->flag;            
            if($request->ajax())
            {
                $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                    "client_detail_masters.client_name",
                                    "printing_schedule_details.sl",
                                    "bag_type_masters.bag_type")
                        ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                        ->Join("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                        ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                        ->leftJoin("printing_schedule_details",function($join){
                            $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                            ->where("printing_schedule_details.lock_status",false);
                        })
                        ->where("roll_details.is_printed",false)
                        ->whereNotNull(DB::raw("json_array_length(roll_details.printing_color)"))                                              
                        ->whereNotNull("roll_details.client_detail_id")
                        ->where("roll_details.lock_status",false)
                        ->where("roll_details.is_printed",false)
                        ->orderBy("printing_schedule_details.sl","ASC")
                        ->orderBy("roll_details.estimate_delivery_date","ASC")
                        ->orderBy("roll_details.client_detail_id","ASC");
                $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('row_color', function ($val) use($flag) {
                        $color = "";
                        if($val->for_client_id && $val->is_printed){
                            $color="tr-client-printed";
                        }elseif($val->is_printed){
                            $color="tr-printed";
                        }
                        elseif($val->for_client_id){
                            $color="tr-client";
                        }                    
                        return $color;
                    })
                    ->addColumn('action', function ($val)use($flag) {                    
                        $button = "";
                        if($val->is_roll_cut){
                            return $button;
                        }
                        if($flag=="printing"){
                            if(!$val->schedule_date_for_print){
                                $button .= '<button class="btn btn-sm btn-warning" onClick="openPrintingScheduleModel('.$val->id.')" >Schedule</button>';
                            }elseif($val->schedule_date_for_print){
                                $button .= '<button class="btn btn-sm btn-danger" onClick="openPrintingScheduleModel('.$val->id.')" >Re-Schedule</button>';
                            }
                        }elseif($flag=="cutting"){
                            if(!$val->schedule_date_for_cutting){
                                $button .= '<button class="btn btn-sm btn-warning" onClick="openCuttingScheduleModel('.$val->id.')" >Schedule</button>';
                            }elseif($val->schedule_date_for_cutting){
                                $button .= '<button class="btn btn-sm btn-danger" onClick="openCuttingScheduleModel('.$val->id.')" >Re-Schedule</button>';
                            }
                        }
                        return $button;
                    })
                    ->addColumn('print_color', function ($val) {                    
                        return collect(json_decode($val->printing_color,true))->implode(",");
                    })
                    ->addColumn("loop_color",function($val){
                        return"";
                    })
                    ->addColumn("gsm_json",function($val){
                        return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                    })
                    ->rawColumns(['row_color', 'action'])
                    ->make(true);
                return $list;
    
            }
            $data["flag"]=$flag;
            return view("Schedule/printing_schedule",$data);
        }catch(Exception $e){

        }
    }

    public function savePrintingSchedule(Request $request){
        try{
            $rules = [
                "rolls" => "required|array",
                "rolls.*.id" => "required|exists:" . $this->_M_RollDetail->getTable() . ",id",
                "rolls.*.position" => "required|integer", // Use `integer` for clarity instead of `int`
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            DB::beginTransaction();
            if($request->rolls){
                $this->_M_PrintingScheduleDetail->where("printing_date",Carbon::now()->format("Y-m-d"))->update(["lock_status"=>true]);
                foreach($request->rolls as $roll){
                    $newRequest = new Request($roll);
                    $newRequest->merge([
                        "roll_id"=>$roll["id"],
                        "printing_date"=>Carbon::now()->format("Y-m-d"),
                        "sl"=> $roll["position"],
                    ]);
                    $this->_M_PrintingScheduleDetail->store($newRequest);
                }
            }
            DB::commit();
            return responseMsgs(true,"successful schedule","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function getRollForCutting(Request $request){
        try{

            $flag= $request->flag;            
            if($request->ajax())
            {
                $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                    "client_detail_masters.client_name",
                                    "cutting_schedule_details.sl",
                                    "bag_type_masters.bag_type")
                        ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                        ->Join("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                        ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                        ->leftJoin("cutting_schedule_details",function($join){
                            $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                            ->where("cutting_schedule_details.lock_status",false);
                        })
                        ->where("roll_details.is_cut",false)
                        ->where(function($where){
                            $where->orWhere(DB::raw(" CASE WHEN roll_details.printing_color IS NOT NULL AND roll_details.is_printed = TRUE THEN TRUE 
                                                           WHEN roll_details.printing_color IS NULL THEN TRUE
                                                        ELSE FALSE END"),true);
                        })
                        ->whereNotNull("roll_details.client_detail_id")
                        ->where("roll_details.lock_status",false)
                        ->orderBy("cutting_schedule_details.sl","ASC")
                        ->orderBy("roll_details.estimate_delivery_date","ASC")
                        ->orderBy("roll_details.client_detail_id","ASC");
                        
                $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('row_color', function ($val) use($flag) {
                        $color = "";
                        if($val->for_client_id && $val->is_printed){
                            $color="tr-client-printed";
                        }elseif($val->is_printed){
                            $color="tr-printed";
                        }
                        elseif($val->for_client_id){
                            $color="tr-client";
                        }                    
                        return $color;
                    })
                    ->addColumn('action', function ($val)use($flag) {                    
                        $button = "";
                        if($val->is_roll_cut){
                            return $button;
                        }
                        if($flag=="printing"){
                            if(!$val->schedule_date_for_print){
                                $button .= '<button class="btn btn-sm btn-warning" onClick="openPrintingScheduleModel('.$val->id.')" >Schedule</button>';
                            }elseif($val->schedule_date_for_print){
                                $button .= '<button class="btn btn-sm btn-danger" onClick="openPrintingScheduleModel('.$val->id.')" >Re-Schedule</button>';
                            }
                        }elseif($flag=="cutting"){
                            if(!$val->schedule_date_for_cutting){
                                $button .= '<button class="btn btn-sm btn-warning" onClick="openCuttingScheduleModel('.$val->id.')" >Schedule</button>';
                            }elseif($val->schedule_date_for_cutting){
                                $button .= '<button class="btn btn-sm btn-danger" onClick="openCuttingScheduleModel('.$val->id.')" >Re-Schedule</button>';
                            }
                        }
                        return $button;
                    })
                    ->addColumn('print_color', function ($val) {                    
                        return collect(json_decode($val->printing_color,true))->implode(",");
                    })
                    ->addColumn("loop_color",function($val){
                        return"";
                    })
                    ->addColumn("gsm_json",function($val){
                        return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                    })
                    ->rawColumns(['row_color', 'action'])
                    ->make(true);
                return $list;
    
            }
            $data["flag"]=$flag;
            return view("Schedule/cutting_schedule",$data);
        }catch(Exception $e){

        }
    }

    public function saveCuttingSchedule(Request $request){
        try{
            $rules = [
                "rolls" => "required|array",
                "rolls.*.id" => "required|exists:" . $this->_M_RollDetail->getTable() . ",id",
                "rolls.*.position" => "required|integer", // Use `integer` for clarity instead of `int`
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            DB::beginTransaction();
            if($request->rolls){
                $this->_M_CuttingScheduleDetail->where("cutting_date",Carbon::now()->format("Y-m-d"))->update(["lock_status"=>true]);
                foreach($request->rolls as $roll){
                    $newRequest = new Request($roll);
                    $newRequest->merge([
                        "roll_id"=>$roll["id"],
                        "cutting_date"=>Carbon::now()->format("Y-m-d"),
                        "sl"=> $roll["position"],
                    ]);
                    $this->_M_CuttingScheduleDetail->store($newRequest);
                }
            }
            DB::commit();
            return responseMsgs(true,"successful schedule","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
