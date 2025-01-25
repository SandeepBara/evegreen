<?php

namespace App\Http\Controllers;

use App\Models\AutoDetail;
use App\Models\BagPacking;
use App\Models\BagPackingTransport;
use App\Models\BagPackingTransportDetail;
use App\Models\BagTypeMaster;
use App\Models\ClientDetailMaster;
use App\Models\OrderPunchDetail;
use App\Models\RollDetail;
use App\Models\TransporterDetail;
use App\Traits\Formula;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PackingController extends Controller
{
    use Formula;
    private $_M_RollDetail;
    private $_M_ClientDetails;
    private $_M_BagType;

    private $_M_BagPacking;
    private $_M_PackTransport;
    private $_M_TransportDetail;
    protected $_M_OrderPunchDetail;
    protected $_M_Auto;
    protected $_M_Transporter;

    function __construct()
    {
        $this->_M_RollDetail = new RollDetail();
        $this->_M_ClientDetails = new ClientDetailMaster();
        $this->_M_BagType = new BagTypeMaster();
        $this->_M_BagPacking = new BagPacking();
        $this->_M_PackTransport  = new BagPackingTransport();
        $this->_M_TransportDetail = new BagPackingTransportDetail();
        $this->_M_OrderPunchDetail = new OrderPunchDetail();
        $this->_M_Auto  =  new AutoDetail();
        $this->_M_Transporter = new TransporterDetail();
    }

    public function packingEnter(Request $request){
        $roll = $this->_M_RollDetail
                    ->where("is_cut",true)
                    ->where("lock_status",false)
                    ->where("is_delivered",false)
                    ->get();
        $clientId = $roll->unique("client_detail_id")->pluck("client_detail_id");
        $data["clientList"] = $this->_M_ClientDetails->whereIn("id",$clientId)->get();
        $bagId =  $roll->unique("bag_type_id")->pluck("bag_type_id");
        $data["bagList"] = $this->_M_BagType->whereIn("id",$bagId)->get();
        return view("Packing/entry",$data);
    }

    public function WIPVerification(Request $request){
        if($request->ajax()){
            $data = $this->_M_OrderPunchDetail
            ->select(DB::raw("order_punch_details.*,client_detail_masters.client_name,bag_type_masters.bag_type,
                            COALESCE(roll_weight,0) as roll_weight,
                            COALESCE(total_garbage,0) as total_garbage,
                            COALESCE(packing_weight,0) as packing_weight,
                            COALESCE(packing_bag_pieces,0) as packing_bag_pieces")
            )
            ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
            ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
            ->join(DB::raw("
                (
                    select sum(net_weight) as roll_weight,order_id
                    from roll_details
                    join order_roll_bag_types on order_roll_bag_types.roll_id = roll_details.id
                    where order_roll_bag_types.lock_status = false and roll_details.is_cut = true
                    group by order_id
                ) as roll
            "),"roll.order_id","order_punch_details.id")
            ->leftJoin(DB::raw("
                (
                    select sum(total_qtr) as total_garbage,order_id
                    from garbage_accept_registers
                    join order_roll_bag_types on order_roll_bag_types.roll_id = garbage_accept_registers.roll_id
                    where order_roll_bag_types.lock_status = false and garbage_accept_registers.lock_status = false
                    group by order_id
                ) as garbage
            "),"garbage.order_id","order_punch_details.id")
            ->leftJoin(DB::raw("
                (
                    select sum(packing_weight) as packing_weight, sum(packing_bag_pieces)as packing_bag_pieces,order_id
                    from bag_packings
                    where lock_status = false
                    group by order_id
                ) As packing
            "),"packing.order_id","order_punch_details.id")
            ->where("order_punch_details.is_delivered",false)
            ->where(DB::raw("COALESCE(roll.roll_weight,0) - COALESCE(packing.packing_weight,0) - COALESCE(garbage.total_garbage,0)"),">",0)
            ->orderBy("order_punch_details.id");
            
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('packing_date', function ($val) { 
                    return $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y") : "";
                })
                ->addColumn('bag_printing_color', function ($val) { 
                    return collect(json_decode($val->bag_printing_color,true))->implode(",") ;
                })
                ->addColumn('bag_size', function ($val) { 
                    return $val->bag_w." X ".$val->bag_l." X ".($val->bag_g ? $val->bag_g:"0.00") ;
                })
                ->addColumn('action', function ($val) {                    
                    $button = "";                    
                    // $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        return view("Packing/wip");
    }

    public function packingEnterWipAdd(Request $request){
        try{
            $rules = [
                "roll"=>"required|array",
                "roll.*.id"=>"required|exists:".$this->_M_OrderPunchDetail->getTable().",id",
                "roll.*.weight"=>"required",
                "roll.*.pieces"=> [                    
                    function ($attribute, $value, $fail) use($request){
                        $key = explode(".",$attribute)[1];
                        $roll = $this->_M_OrderPunchDetail->find($request->roll[$key]["id"]);
                        if($roll && $roll->units=="Piece" && (!$value))
                        {
                            $fail('The '.$attribute.' id required');
                        }
    
                    },
                ],
            ];
            
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $user = Auth()->user();
            DB::beginTransaction();
            foreach($request->roll as $val){
                $newRequest = new Request();
                $newRequest->merge([
                    "packing_weight"=>$val["weight"],
                    "packing_bag_pieces"=>$val["pieces"],
                    "order_id"=>$val["id"],
                    "user_id"=>$user->id,
                ]);

                $this->_M_BagPacking->store($newRequest);
            }
            DB::commit();
            return responseMsgs(true,"Bag Entry Successful","");
            
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function searchRoll(Request $request){
        try{
            $roll=$this->_M_RollDetail->select("roll_details.*","bag_type_masters.bag_type")
                    ->join("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.is_cut",true)
                    ->where("roll_details.is_delivered",false)
                    ->where("roll_details.client_detail_id",$request->clientId);
            if($request->l){
                $roll->where("roll_details.l",$request->l);
            }
            if($request->w){
                $roll->where("roll_details.w",$request->w);
            }
            if($request->bag_type_id){
                $roll->where("roll_details.bag_type_id",$request->bag_type_id);
            }
            $roll = $roll->get();
            return responseMsgs(true,"data Fetch",$roll);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function packingEnterAdd(Request $request){        
        try{
            $rules = [
                "roll"=>"required|array",
                "roll.*.id"=>"required|exists:".$this->_M_RollDetail->getTable().",id",
                "roll.*.weight"=>"required",
                "roll.*.pieces"=> [                    
                    function ($attribute, $value, $fail) use($request){
                        $key = explode(".",$attribute)[1];
                        $roll = $this->_M_RollDetail->find($request->roll[$key]["id"]);
                        if($roll && $roll->bag_unit=="Piece" && (!$value))
                        {
                            $fail('The '.$attribute.' id required');
                        }
    
                    },
                ],
            ];
            
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $user = Auth()->user();
            DB::beginTransaction();
            foreach($request->roll as $val){
                $newRequest = new Request();
                $newRequest->merge([
                    "packing_weight"=>$val["weight"],
                    "packing_bag_pieces"=>$val["pieces"],
                    "roll_id"=>$val["id"],
                    "user_id"=>$user->id,
                ]);

                $this->_M_BagPacking->store($newRequest);
            }
            DB::commit();
            return responseMsgs(true,"Bag Entry Successful","");
            
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function bagStock(Request $request){
        $user = Auth()->user();
        $user_type = $user->user_type;
        if($request->ajax()){
            $data = $this->_M_BagPacking->select("order_punch_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name"
                    )
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
                    ->where("bag_packings.packing_status",1)
                    ->where("bag_packings.lock_status",false);
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('packing_date', function ($val) { 
                    return $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y") : "";
                })
                ->addColumn('bag_color', function ($val) { 
                    return collect(json_decode($val->bag_color,true))->implode(",") ;
                })
                ->addColumn('bag_size', function ($val) { 
                    return $val->bag_w +($val->bag_g ? $val->bag_g :0) ." X ". $val->bag_l;
                })
                ->addColumn('action', function ($val) {                    
                    $button = "";                    
                    // $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        return view("Packing/stock");
    }

    public function bagGodown(Request $request){
        $user = Auth()->user();
        $user_type = $user->user_type;
        if($request->ajax()){
            $data = $this->_M_BagPacking->select("order_punch_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name"
                    )
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
                    ->where("bag_packings.packing_status",2)
                    ->where("bag_packings.lock_status",false);
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('packing_date', function ($val) { 
                    return $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y") : "";
                })
                ->addColumn('bag_color', function ($val) { 
                    return collect(json_decode($val->printing_color,true))->implode(",") ;
                })
                ->addColumn('bag_size', function ($val) { 
                    return $val->bag_w +($val->bag_g ? $val->bag_g :0) ." X ". $val->bag_l;
                })
                ->addColumn('action', function ($val) {                    
                    $button = "";                    
                    // $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        return view("Packing/godown");
    }

    public function reivingGodown(Request $request){
        if($request->ajax()){
            $data = $this->_M_PackTransport->select("bag_packing_transports.id","bag_packing_transports.bill_no","bag_packing_transports.invoice_no","bag_packing_transports.transport_date",
                        DB::raw(
                            "
                            COUNT(bag_packing_transport_details.bag_packing_id) AS total_bag,
                            COUNT( CASE WHEN bag_packing_transport_details.is_delivered = FALSE THEN bag_packing_transport_details.bag_packing_id END ) AS total_unverified_bag
                            "
                        )
                    )
                    ->join("bag_packing_transport_details","bag_packing_transport_details.pack_transport_id","bag_packing_transports.id")
                    ->where("bag_packing_transports.transport_status",3)
                    ->where("bag_packing_transport_details.lock_status",false)
                    ->where("bag_packing_transports.is_fully_reviewed",false)
                    ->where("bag_packing_transports.lock_status",false)
                    ->groupBy("bag_packing_transports.id","bag_packing_transports.bill_no","bag_packing_transports.invoice_no","bag_packing_transports.transport_date");
                
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->addColumn('action', function ($val) {                 
                    return '<button class="btn btn-sm btn-info" onClick="openReceivingModel('.$val->id.')" >Verify</button>';
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;
        }
        return view("Packing/reivingGodown");
    }

    public function reivingTransport(Request $request){
        try{
            $data = $this->_M_TransportDetail->select("bag_packings.*","bag_packing_transport_details.*")
                    ->join("bag_packings","bag_packings.id","bag_packing_transport_details.bag_packing_id")
                    ->where("bag_packing_transport_details.pack_transport_id",$request->id)
                    ->where("bag_packing_transport_details.lock_status",false)
                    ->where("bag_packing_transport_details.is_delivered",false)
                    ->get();
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function addInGodown(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                "id"=>"required|exists:".$this->_M_TransportDetail->getTable().",id,is_delivered,false",
            ]);
            if($validate->fails()){
                return validationError($validate);
            }
            $currentDate = Carbon::now()->format("Y-m-d");
            $user = Auth()->user();

            $transportDtl = $this->_M_TransportDetail->find($request->id);
            $bagPackage = $this->_M_BagPacking->find($transportDtl->bag_packing_id);
            $transport = $this->_M_PackTransport->find($transportDtl->pack_transport_id);

            $transportDtl->is_delivered = true;
            $transportDtl->reiving_user_id = $user->id;
            $transportDtl->reiving_date = $currentDate;

            $bagPackage->packing_status = 2;
            
            DB::beginTransaction();
            $transportDtl->update();
            $bagPackage->update();
            $test = $this->_M_TransportDetail->where("is_delivered",false)->where("pack_transport_id",$transportDtl->pack_transport_id)->count("id");
            if($test==0){
                $transport->is_fully_reviewed = true;
                $transport->reiving_date = $currentDate;
                $transport->reiving_user_id = $user->id;
            }
            $transport->update();
            DB::commit();
            return responseMsgs(true,"Bag Verify","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function bagTransport(Request $request){
        $flag = $request->flag;
        if($request->ajax()){
            return $this->bagGodown($request);
        }
        $data["autoList"] =$this->_M_Auto->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["flag"] = $flag;
        return view("Packing/transport",$data);
    }

    public function bagStockToGodown(Request $request){
        $flag = $request->flag;
        if($request->ajax()){
            return $this->bagStock($request);
        }
        $data["autoList"] =$this->_M_Auto->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["flag"] = $flag;
        return view("Packing/transportStoke",$data);
    }

    public function addBagInTransport(Request $request){        
        return view("Packing/inTransport");
    }

    public function searchPackingForTransport(Request $request){
        try{
            $packingStatus = [];
            if($request->status=="For Godown"){
                $packingStatus = [1];
            }
            if($request->status=="For Delivery"){
                $packingStatus = [1,2];
            }
            DB::enableQueryLog();
            $data = $this->_M_BagPacking
                    ->select("order_punch_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name"
                    )
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
                    ->where("bag_packings.packing_no",$request->packingNo)
                    ->whereIn("bag_packings.packing_status",$packingStatus)
                    ->whereIn("bag_packings.packing_status",[1,2])
                    ->where("bag_packings.lock_status",false)
                    ->first();
            // dd(DB::getQueryLog());
            if($data){
                $data->printing_color = collect(json_decode($data->printing_color,true))->implode(",");
            }
            return responseMsgs(true,"Data Fetch",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function transportAdd(Request $request){
        try{
            $user = Auth()->user();
            $rules = [
                "transPortType" => "required|in:For Godown,For Delivery", // Fixed 'id' to 'in' for a set of allowed values
                "dispatchedDate" => "required|date", // Ensures dispatchedDate is a valid date
                "invoiceNo" => "required", // Invoice number is mandatory
                "billNo" => "required_if:transPortType,For Delivery", // Bill number is required only if transport type is 'For Delivery'
                "bag" => "required|array", // Packing must be a non-empty array
                "bag.*.id" => [
                        "required",
                        Rule::exists($this->_M_BagPacking->getTable(), "id")
                            ->whereIn("packing_status", [1, 2]),
                    ], // Ensures the packing IDs exist with specific statuses
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $transportStatus = Config::get("customConfig.transportType.".$request->transPortType);
            $request->merge(["userId"=>$user->id,"transportStatus"=>$transportStatus]);
            DB::beginTransaction();

            $tranId = $this->_M_PackTransport->store($request);
            $orderId=collect();
            foreach($request->bag as $val){
                $packing = $this->_M_BagPacking->find($val["id"]);
                $orderId->push($packing->order_id);
                $newRequest = new Request($val);
                $newRequest->merge([
                    "packTransportId"=>$tranId,
                    "bagPackingId"=>$packing->id,
                ]);
                $this->_M_TransportDetail->store($newRequest);
                $packing->packing_status = $transportStatus ;
                $packing->update();
            }
            if(Config::get("customConfig.transportType.For Delivery")==$transportStatus){
                $orderId= $orderId->unique();
                foreach($orderId as $val){
                    $order = $this->_M_OrderPunchDetail->find($val);
                    $totalDelivered = $this->_M_BagPacking
                                    ->where("order_id",$val)
                                    ->where("packing_status",Config::get("customConfig.transportType.For Delivery"))
                                    ->get();
                    $totalUnit = $totalDelivered->sum("packing_weight");
                    if($order->units!="Kg"){
                        $totalUnit = $totalDelivered->sum("packing_bag_pieces");
                    }                    
                    $bookedRoll = $order->getRollDetail()->get();
                    $totalGarbage = 0;
                    $garbagePossibleBagPiece =0;
                    $rollWeight = 0;
                    $bag = $order->getBagType();
                    $bagPiecesFormula = $bag->roll_find;
                    foreach($bookedRoll as $roll){
                        $acceptedGarbage = $roll->getAcceptedGarbage()->sum("total_qtr");
                        $notAcceptedGarbage = $roll->getNotAcceptedGarbage()->sum("total_qtr");
                        $totalGarbage +=($acceptedGarbage+$notAcceptedGarbage);
                        
                        $newPiecesRequest = new Request($roll->toArray());
                        $newPiecesRequest->merge([
                            "formula"=>$bagPiecesFormula,
                            "bookingBagUnits"=>"Pieces",
                            "length" => $roll->length,
                            "netWeight" => $roll->net_weight,
                            "size" => $roll->size,
                            "gsm" => $roll->gsm,
                            "bagL"=> $order->bag_l,
                            "bagW"=> $order->bag_w,
                            "bagG"=> $order->bag_g,
                        ]);
                        $result = $this->calculatePossibleProduction($newPiecesRequest);
                        $garbagePec = (($acceptedGarbage+$notAcceptedGarbage)/$roll->net_weight);
                        // $garbagePossibleBagPiece =0;
                        if($garbagePec){
                            $garbagePossibleBagPiece += $result["result"] * $garbagePec;
                        }

                        // dd($result,$garbagePossibleBagPiece,$roll->net_weight,$acceptedGarbage+$notAcceptedGarbage,$garbagePec);

                        // $formula = "(W X RS X GSM)/1550";
                        // $bag = $
                        // $request->merge(
                        //     [
                        //         "length"=>$roll->length,
                        //         "netWeight"=>$roll->net_weight,
                        //         "size"=>$roll->size,
                        //         "gsm"=>$roll->gsm,
                        //         "bagL"=>$order->bag_l,
                        //         "bagW"=>$order->bag_w,
                        //         "bagG"=>$order->bag_g,
                        //         "formula"=>$formula
                        //     ]
                        // );
                        // $result = $this->calculatePossibleProduction($request);
                        // dd($result,$roll,$acceptedGarbage,$notAcceptedGarbage);
                    }
                    if(($order->total_units - $order->disbursed_units)<=round($totalUnit + ($order->units=="Kg" ? $totalGarbage : $garbagePossibleBagPiece))){
                        $order->is_delivered = true;
                        $order->delivery_date = Carbon::now()->format("Y-m-d");
                    }
                    $order->update();
                }
            }
            DB::commit();
            return responseMsgs(true,"Dag is Dispatched","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function transportRegister(Request $request){
        
        if($request->ajax())
        {
            $data = $this->_M_PackTransport->select("bag_packing_transports.*","auto_details.auto_name",
                        "transporter_details.transporter_name"
                    )
                    ->leftJoin("auto_details","auto_details.id","bag_packing_transports.auto_id")
                    ->leftJoin("transporter_details","transporter_details.id","bag_packing_transports.transporter_id")
                    ->where("bag_packing_transports.lock_status",false)
                    ->orderBy("bag_packing_transports.id","DESC");
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("transition_type",function($val){
                    $transition_type = "";
                    if($val->transport_status==3){
                        $transition_type="factory to godown";
                    }
                    if($val->transport_status==4){
                        $transition_type="dispatched for delivery";
                    }
                    return $transition_type;
                })
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->addColumn('bag_no', function ($val) { 
                    return collect($val->getBag()->get())->pluck("packing_no")->implode(" , ") ;
                })
                ->addColumn('client_name', function ($val) { 
                    $bag = $val->getBag()->get();
                    $orderId =$bag->unique("order_id")->pluck("order_id");
                    $order = $this->_M_OrderPunchDetail->whereIn("id",$orderId)->get();
                    $clineId = $order->unique("client_detail_id")->pluck("client_detail_id");
                    $client = $this->_M_ClientDetails->whereIn("id",$clineId)->get();
                    return collect($client)->pluck("client_name")->implode(" , ") ;
                })
                ->addColumn('action', function ($val) {                    
                    $button = "";                    
                    // $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data["autoList"] = $this->_M_Auto->getAutoListOrm()->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->getAutoListOrm()->orderBy("id","ASC")->get();
        return view("Packing/bag_transport",$data);
    }
}
