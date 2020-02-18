<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;
use App\Movement;
use App\Http\Resources\Movement as MovementResource;
use App\Http\Controllers\Requests\DebitMovementRequest;
use App\User;
use App\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class MovementControllerAPI extends Controller
{

    public function index(Request $request)
    {
       if ($request->has('page')) {
            return MovementResource::collection(Movement::paginate(5));
       } else {
            return MovementResource::collection(Movement::all());
        }
    }
    public function list()
    {
        $user = Auth::user();

        return MovementResource::collection(
            Movement::where('wallet_id', Auth::id())
            ->orderBy('date', 'desc')->get()
                //->paginate(15)
        );
    }

    public function show($id)
    {
        $movements = Auth::user()->wallet->movement;
        return MovementResource::collection($movements);
    }


    /*public function getUserMovements($id){
        $movements = Auth::user()->wallet->movement->with('category', 'transfer_wallet', 'transfer_wallet.user')->orderBy('date', 'desc')->paginate(10);
        return $movements;
    }*/

    public function store(Request $request)
    {

    }


    public function getFilteredMovements(Request $request){

        if(!is_null($request->id) || !is_null($request->type) || !is_null($request->category) || !is_null($request->type_payment) || !is_null($request->transfer_email) || !is_null($request->data_inf) || !is_null($request->data_sup) /*|| !is_null($request->value) */){

            $movements = Movement::with('category', 'transfer_wallet', 'transfer_wallet.user')->select('*')->where('wallet_id', $request->user_id);


          /*  if (!is_null($request->value)){
                $movements = $movements->where('value', 'like', $request->value);
            }*/


            if (!is_null($request->id)){
                $movements = $movements->where('id', 'like', $request->id . '%');
            }
            if (!is_null($request->type)){
                $movements = $movements->where('type', $request->type);
            }
            if (!is_null($request->category)){
                $category = DB::table('categories')->select('id')->where('id', $request->category)->get();
                if($category->isEmpty()){
                    return 'Category does not exist!';
                }
                $movements = $movements->where('category_id', $category[0]->id);
            }
            if (!is_null($request->type_payment)){
                $movements = $movements->where('type_payment', $request->type_payment);
            }
            if (!is_null($request->transfer_email)){
                $transfer_email = DB::table('wallets')->select('id')->where('email', $request->transfer_email)->get();
                if($transfer_email->isEmpty()){
                    return 'Transfer e-mail does not exist!';
                }
                $movements = $movements->where('transfer_wallet_id', $transfer_email[0]->id);
            }
            if (!is_null($request->data_sup)){
                $movements = $movements->where('date', '>=', $request->data_sup);
            }
            if (!is_null($request->data_inf)){
                $movements = $movements->where('date', '<=', $request->data_inf);
            }

            $movements = $movements->orderBy('date', 'desc')->paginate(10);

        }else{
            $movements = Movement::with('category', 'transfer_wallet', 'transfer_wallet.user')->select('*')->where('wallet_id', $request->user_id)->orderBy('date', 'desc')->paginate(10);
        }

        return $movements;
    }

    public function update(Request $request, $id){
        $movement = Movement::findOrFail($id);

        $category_name = $request->category['name'];
        $category =  DB::table('categories')->select('id')->where('name', $category_name)->where('type', $movement->type)->get();
        if($category->isEmpty() && $movement->category!=null){
            return 'Category does not exist for this type of movement';
        }
        if($movement->category!=null){
            $movement->category_id = $category[0]->id;
        }
        $movement->description = $request->description;

        $movement->save();
        return new MovementResource($movement);
    }


    public function createDebit(DebitMovementRequest $request) {
        $movement = new Movement();
        if($request->type_payment == "bt"){
            $movement->fill($request->except(['destination_email',"email",'mb_entity_code','mb_payment_reference','description',"source_description"]));
        }
        if($request->type_payment == "mb"){
            $movement->fill($request->except(['destination_email',"email",'iban','source_description']));
        }

        if($request->email == $request->destination_email){
            return response()->json(["errors"=> ["balance" =>["Email and Destination Email are the same!"]]], 400);
        }

        $wallet = Wallet::where('email',$request->email)->first();
        if($wallet == null){
            return response()->json(["errors"=> ["email" =>[ "Email is not valid!"]]], 400);
        }

        if($wallet->balance <= 0){

            return response()->json(["errors"=> ["balance" =>["The balance is less or equal to 0! Debit not allowed"]]], 400);
        }

        if($wallet->balance - $request->value < 0){
            return response()->json(["errors"=> ["balance" =>["The balance would be negative with this debit! Debit not allowed"]]], 400);
        }




        $date = Carbon::now();
        $movement->wallet_id = $wallet->id;
        $movement->type = "e";
        $movement->start_balance = $wallet->balance;
        $movement->end_balance = $wallet->balance - $request->value;
        $movement->date = $date->toDateTimeString();
        $movement->save();

        $wallet->balance = $wallet->balance - $request->value;
        $wallet->save();


        if($request->transfer == 1){

            $wallet_dest = Wallet::where('email',$request->destination_email)->first();
            if($wallet_dest == null){
                return response()->json(["error"=> "Destination Email is invalid!"], 400);
            }
            if($wallet->balance - $request->value > 1000){

                $wallet_dest = Wallet::where('msg',$request->msg)->first();

            }

            $date = Carbon::now();

            $movement_dest = new Movement();
            $movement_dest->fill($request->except(['destination_email',"email"]));
            $movement_dest->wallet_id = $wallet_dest->id;
            $movement_dest->type = "i";
            $movement_dest->start_balance = $wallet_dest->balance;
            $movement_dest->end_balance = $wallet_dest->balance + $request->value;
            $movement_dest->date = $date->toDateTimeString();
            $movement_dest->transfer_movement_id = $movement->id;
            $movement_dest->transfer_wallet_id = $wallet->id;
            $movement_dest->source_description = $request->source_description;

            $movement_dest->save();

            $wallet_dest->balance = $wallet_dest->balance + $request->value;
            $wallet_dest->save();

            $movement->source_description = $request->source_description;
            $movement->transfer_movement_id = $movement_dest->id;
            $movement->transfer_wallet_id = $wallet_dest->id;

            $movement->save();
            return response()->json(["id"=> $wallet_dest->id, "email" => $wallet_dest->email], 201);
        }
        return response()->json(null, 201);
    }

    public function getMovementStatistics(){
        $time = strtotime("-1 year", time());
        $date = date("Y-m-d", $time);
        $authenticatedUser = Auth::guard('api')->user();
        if ($authenticatedUser == null || $authenticatedUser->type != "a") {
            return abort(401);
        }
        $total = Movement::whereDate("date", ">=" ,$date)->count();
        $totalByCategory = [];
        $categories = Category::all();
        for ($i = 0; $i < sizeof($categories); $i++) {
            $cat = $categories[$i]->name;
            $val = Movement::where('category_id','=',$categories[$i]->id)->whereDate("date", ">=" ,$date)->count() / $total;
            $val = round($val,2) * 100;
            $obj = (object) array(
                'category' => $cat,
                'total' => Movement::where('category_id','=',$categories[$i]->id)->whereDate("date", ">=" ,$date)->count()
            );
            array_push($totalByCategory,$obj);
        }
        $totalExpenses = round(Movement::where('type','=','e')->whereDate("date", ">=" ,$date)->count()/$total,2)*100;
        $totalIncomes =round(Movement::where('type','=','i')->whereDate("date", ">=" ,$date)->count()/$total,2)*100;
        $totalTransfers = round(Movement::where('transfer','=',1)->whereDate("date", ">=" ,$date)->count()/$total,2)*100;
        $totalNonTransfers = round(Movement::where('transfer','=',0)->whereDate("date", ">=" ,$date)->count()/$total,2)*100;
        $largestMovementValue = DB::select('select max(value) as maximum from movements')[0]->maximum;
        $smallestMovementValue = DB::select('select min(value) as minimum from movements')[0]->minimum;
        $averageMovementValue = DB::select('select avg(value) as average from movements')[0]->average;
        $data = (object) array(
            'total' => $total,
            'totalExpenses' => $totalExpenses,
            'totalIncomes' => $totalIncomes,
            'totalTransfers' => $totalTransfers,
            'totalNonTransfers' => $totalNonTransfers,
            'largestMovement' => floatval($largestMovementValue),
            'smallestMovement' => floatval($smallestMovementValue),
            'averageMovementValue' => round(floatval($averageMovementValue),2),
            'totalByCategory' => $totalByCategory,
        );
        $response = json_encode($data);
        return $response;
    }

    public function getAllUserMovements(Request $request){
        $time = strtotime("-1 year", time());
        $date = date("Y-m-d", $time);
        $wallet = Wallet::where('email','=',$request->email)->first();
        $movements = Movement::where('wallet_id','=',$wallet->id)->whereDate("date", ">=" ,$date)->orderBy('date','ASC')->get();
        return $movements;
    }


    public function totalMovements(){
        $time = strtotime("-1 year", time());
        $date = date("Y-m-d", $time);
        $data = DB::table('movements')->whereDate("date", ">=" ,$date)->count();
        return $data;
    }


}

