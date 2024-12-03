<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MenuController;
use App\Models\MenuMaster;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    protected $_M_User;
    protected $_M_MenuMaster;

    function __construct()
    {
        $this->_M_User = new User();
        $this->_M_MenuMaster = new MenuMaster();
    }

    public function hasPassword(Request $request){
        try{
            DB::enableQueryLog();
            $datas =  $this->_M_User::select('id', 'password')
                ->where('password',12345)
                ->orderby('id')
                ->get();
            DB::beginTransaction();
            foreach ($datas as $data) {
                $user = User::find($data->id);     
                $user->password = Hash::make(12345);
                $user->update();
                
            }
            DB::commit();
        }catch(Exception $e){
            dd("jjj");
        }
    }

    public function login(Request $request){
        try{
            $data = $request->all();
            if($request->getMethod()=="GET"){
                return view("User/login",$data);
            }
            elseif($request->getMethod()=="POST"){

                $validate = Validator::make($request->all(),
                    [
                        'email' => 'required|email',
                        'password' => 'required|confirmed',
                    ]
                    );
                if($validate->failed()){                    
                    return redirect()->back()
                        ->withErrors($validate->failed())
                        ->withInput();
                }
                if($user = $this->_M_User->where("email",$request->email)->first()){
                    if(!(Hash::check($request->password, $user->password))){
                        flashToast("message","Invalid User");
                        return redirect()->back()
                            ->withErrors(["email"=>"Invalid password!.."])
                            ->withInput();
                    }
                    $credentials = $request->only('email', 'password');

                    
                    if (Auth::attempt($credentials)) {
                        $menuList =""; Redis::get("menu_list_".$user["user_type_id"]);                        
                        if (!$menuList) {
                            $pemitedMenu = $user->getMenuList()->get();
                            
                            $menuId = $pemitedMenu->unique("menu_master_id")->pluck("menu_master_id");
                            $menus = $this->_M_MenuMaster->whereIn("id",$menuId)
                                                            ->where("lock_status",false)
                                                            ->get();                                         
                            $tree = (new MenuController())->generateMenuTree($menus);
                            Redis::set("menu_list_".$user["user_type_id"],$tree);
                        }
                        flashToast("message","success");
                        return redirect()->to('/home');
                    }
                }else{
                    flashToast("message","Invalid User");
                    return redirect()->back()
                        ->withErrors(["email"=>"Invalid Email Id!.."])
                        ->withInput();
                }
            }
            
        }catch(Exception $e){
            flashToast("message","Internal Server Error");
            return redirect()->back();
        }

    }

    /**
     * Log the user out.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::guard('web')->logout();

        // Auth::logout();
        return redirect()->route('login');
    }

    public function profile(){
        return redirect()->back();
    }
    public function changePassword(){
        return redirect()->back();
    }

    public function createUser(Request $request){
        try{
            $data = [];
            if($request->getMethod()=="GET"){
                
                return view("User/create",$data);
            }
            dd($request->all(),$request->getMethod());

        }catch(Exception $e){

        }
    }
}
