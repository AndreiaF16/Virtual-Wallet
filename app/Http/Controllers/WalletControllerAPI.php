<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;
use App\Wallet;
use App\Http\Resources\Wallet as WalletResource;
use Hash;
use App\User;
use Illuminate\Support\Facades\Auth;

class WalletControllerAPI extends Controller
{

    public function index(){
     $wallets = DB::table('wallets')->count();
        return $wallets;




       // $wallets = DB::table('wallets')->select('balance')->get();
       $sizeWallets = count($wallets);
        $totalBalance = 0;
        for($i = 0; $i < $sizeWallets; $i++){
            $totalBalance = $totalBalance + $wallets[$i]->balance;
        }
        $data = number_format((float)$totalBalance, 2, '.', '');
        return $data;
       /* $user=Wallet::findOrFail($id);

        if((Auth::guard('api')->user()->id != $user->id) /*|| (Auth::guard('api')->user()->type != 'u')*///){
         /*   return Response::json([
                'unauthorized' => 'Access forbiden!'
            ], 401);
        }*/


     /*   if ($request->has('page')) {
            return WalletResource::collection(Wallet::paginate(5));
        } else {
            return WalletResource::collection(Wallet::all());
        }*/

    }

    /*public function movementsWithCategoriesandUsers() {
        $myWallets = Movement::join('movements', 'wallets.id', '=', 'movements.wallet_id')
        ->join('categories', 'categories.id', '=', 'movements.category_id')->where('id', '=', 'movements.category_id')
        ->get(['movements.*']);
        return MovementResource::collection($myWallets);

    }*/

    public function showVirtualWallet(/* $request*/)
	{
        // ------------------- 1ª OPÇÃO ---------------------------

        /*$user = Auth::user();//->wallet;
        return WalletResource::collection(Wallet::where("id",$user->id)->get());*/

        /// ---------------------- 2ª OPÇÃO ------------------------
        $wallet = Auth::user()->wallet;
        return new WalletResource($wallet);
        //----------------------------------------------------------

    //   $user=Wallet::findOrFail();

      /*  if((Auth::guard('api')->user()->id != $user->id) /*|| (Auth::guard('api')->user()->type != 'u')*///){
         //   return Response::json([
          //      'unauthorized' => 'Access forbiden!'
          //  ], 401);
      //  }*/
		/*if(Auth::guard('api')->user()->type != 'u'){
			return Response::json([
				'unauthorized' => 'Access forbiden! Only Plataform Users are allowed'
			], 401);
        }*/
    }

    /*public function myWallets($id){

        $myWallets = Wallet::where('id', '=', $id)->get();
        return Wallet::collection($myWallets);
    }*/
}
