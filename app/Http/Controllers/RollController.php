<?php

namespace App\Http\Controllers;

use App\Exports\ExportRoll;
use App\Imports\RollDetailsImport;
use App\Models\BagType;
use App\Models\ClientDetail;
use App\Models\RollDetail;
use App\Models\RollPrintColor;
use App\Models\VendorDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class RollController extends Controller
{
    private $_M_RollDetail;
    private $_M_VendorDetail;
    private $_M_RollPrintColor;
    private $_M_ClientDetails;
    private $_M_BagType;

    function __construct()
    {
        $this->_M_RollDetail = new RollDetail();
        $this->_M_VendorDetail= new VendorDetail();
        $this->_M_RollPrintColor = new RollPrintColor();
        $this->_M_ClientDetails = new ClientDetail();
        $this->_M_BagType = new BagType();
    }

    public function addRoll(Request $request){
        try{
            if($request->getMethod()=="POST"){
                $rule = [
                    "rollNo"=>"required|unique:".$this->_M_RollDetail->getTable().",roll_no",
                    "purchaseDate"=>"nullable|",
                    "vendorId"=>"required|exists:".$this->_M_VendorDetail->getTable().",id,lock_status,false",
                    "rollSize"=>"required|numeric|min:0.1",
                    "rollGsm"=>"required|numeric|min:0.01",
                    "rollColor"=>"required",
                    "rollLength"=>"required|numeric|min:0.1",
                    "netWeight"=>"required|numeric|min:0.1",
                    "grossWeight"=>"required|numeric|min:0.1"
                ];
                $validate = Validator::make($request->all(),$rule);
                if($validate->fails()){
                    return validationError($validate);
                }         
                $id = $this->_M_RollDetail->store($request);
                if($request->forClientId){
                    foreach($request->printingColor as $val){
                        $newRequest = new Request(["roll_id"=>$id,"color"=>$val]);
                        $this->_M_RollPrintColor->store($newRequest);
                    }
                }
                $roll = $this->_M_RollDetail->find($id);
                flashToast("message","New Roll Add");
                return responseMsgs(true,"New Roll Added",["rollDtl"=>$roll]);
            }
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function importRoll(Request $request){
        try{
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');

            // Import the CSV file using the RollDetailsImport class
            DB::beginTransaction();
            Excel::import(new RollDetailsImport, $file);
            DB::commit();
            return responseMsgs(true,"data import","");

        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    function rollBook(Request $request){
        try{
            $rule=[
                "rollId"=>"required|exists:".$this->_M_RollDetail->getTable().",id,lock_status,false",
                "bookingForClientId"=>"required|exists:".$this->_M_ClientDetails->getTable().",id,lock_status,false",
                "bookingBagUnits"=>"required|in:Kg,Pice",
                "bookingBagTypeId"=>"required|exists:".$this->_M_BagType->getTable().",id",
                "bookingPrintingColor"=>"required|array",
                "bookingPrintingColor.*"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            $roll = $this->_M_RollDetail->find($request->rollId);
            $roll->for_client_id = $request->bookingForClientId;
            $roll->printing_description = $request->bookingPrintingDescription;            
            $roll->bag_type_id = $request->bookingBagTypeId;
            $roll->bag_units = $request->bookingBagUnits;

            DB::beginTransaction();
            $roll->update();
            if($request->bookingPrintingColor){
                $this->_M_RollPrintColor->where("roll_id",$roll->id)->update(["lock_status"=>true]);
                foreach($request->bookingPrintingColor as $color){
                    $newRequest = new Request(["roll_id"=>$roll->id,"color"=>$color]);
                    $this->_M_RollPrintColor->store($newRequest);
                }
            }
            DB::commit();
            return responseMsgs(true,"Roll No. ".$roll->roll_no." is Booked","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollList(Request $request){
        $flag= $request->flag;
        if($request->ajax()){
            // dd($request->ajax());
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_details.vendor_name","client_details.client_name","bag_types.bag_type")
                    ->join("vendor_details","vendor_details.id","roll_details.vendor_id")
                    ->leftJoin("client_details","client_details.id","roll_details.for_client_id")
                    ->leftJoin("bag_types","bag_types.id","roll_details.bag_type_id")
                    ->where("roll_details.lock_status",false)
                    ->orderBy("roll_details.id","DESC");
            if($flag!="history"){
                $data->where("roll_details.is_roll_cut",false);
            }
            if($flag=="history" ){
                $fromDate = $request->fromDate;
                $uptoDate = $request->uptoDate;
                if($fromDate && $uptoDate){              
                    $data->whereBetween("purchase_date",[$fromDate,$uptoDate]);
                }
                elseif($fromDate){
                    $data->where("purchase_date",">=",$fromDate);
                }
                elseif($uptoDate){
                    $data->where("purchase_date","<=",$uptoDate);
                }
            }
            // if($flag=="booking"){
            //     $data->whereNull("roll_details.for_client_id");
            // }
            if ($request->has('export')) {
                // Skip pagination when exporting
                $data = $data->get();
                return Excel::download(new ExportRoll($data), 'roll.xlsx');
            }

            // Handling search
            if ($request->has('search')) {
                $search = $request->search['value'];  // search term from DataTables
                
                $data = $data->where(function ($query) use ($search) {

                    $query->where("roll_details.roll_no","LIKE", "%$search%")
                        ->orWhere("roll_details.purchase_date","LIKE", "%$search%")
                        ->orWhere("roll_details.roll_size","LIKE", "%$search%")
                        ->orWhere("roll_details.roll_gsm","LIKE", "%$search%")
                        ->orWhere("roll_details.roll_color","LIKE", "%$search%")
                        ->orWhere("roll_details.roll_length","LIKE", "%$search%")
                        ->orWhere("roll_details.net_weight","LIKE", "%$search%")
                        ->orWhere("roll_details.gross_weight","LIKE", "%$search%")
                        ->orWhere('vendor_details.vendor_name', 'LIKE', "%$search%")
                        ->orWhere('client_details.client_name', 'LIKE', "%$search%")
                        ->orWhere('bag_types.bag_type', 'LIKE', "%$search%");  // Assuming ststop is a field to search
                });
            }
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) {
                    $color = "";
                    if($val->for_client_id){
                        $color="tr-client";
                    }
                    if($val->is_printed){
                        $color="tr-printed";
                    }
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }
                    return $color;
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect($val->getPrintingColor()->get())->implode("color",",");
                })
                ->addColumn('action', function ($val) {
                    $user_type = Auth()->user()->user_type_id??"";
                    $button = "";
                    if($val->is_roll_cut){
                        return $button;
                    }
                    if(in_array($user_type,[1,2]) && !$val->for_client_id){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="openModelBookingModel('.$val->id.')" >Book</button>';
                    }if(in_array($user_type,[1,2]) && $val->for_client_id){
                        $button .= '<button class="btn btn-sm btn-danger" onClick="openModelAlterBookingModel('.$val->id.')" >Alter Booking</button>';
                    }
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data["flag"]=$flag;
        return view("Roll/list",$data);
    }

}
