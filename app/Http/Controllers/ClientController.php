<?php

namespace App\Http\Controllers;

use App\Models\ClientDetail;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
//
    private $_M_ClientDetail;
    function __construct()
    {
        $this->_M_ClientDetail = new ClientDetail();
    }

    public function addClient(Request $request){
        try{
            $id = $request->id;
            if($request->id){
                $this->_M_ClientDetail->edit($request);
            }else{
                $id =  $this->_M_ClientDetail->store($request);
            }
            $data["client"]=$this->_M_ClientDetail->find($id);
            return responseMsgs(true,"New Client Add",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function clientList(Request $request){
        try{
            if($request->ajax()){
                $data = $this->_M_ClientDetail->where("lock_status",false);
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($val) {
                        return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>';
                    })->rawColumns(['action'])
                    ->make(true);
            }
            return view("Client/list");
        }catch(Exception $e){
            flashToast("message","Internal Server Error");
            return redirect()->back();
        }
    }

    public function getClientDtl($id,Request $request){
        try{
            $data = $this->_M_ClientDetail->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

}
