<?php

namespace App\Http\Controllers;

use App\User;
use App\Wallet;
use App\Movement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Movement as MovementResource;
use App\Http\Controllers\Requests\IncomeOperatorRequest;

class OperatorControllerAPI extends Controller
{
    public function registerIncome(IncomeOperatorRequest $request){
        $movement = new Movement();

        if($request->type_payment != 'bt'){
            $movement->fill($request->except(['iban','source_description']));
        }else{
            $movement->fill($request->all());
        }

        //Alterar balance da wallet destino
        $wallet = Wallet::where('email',$request->email)->first();
        if($wallet == null){
            return response()->json(["errors"=> ["email" =>["Email does not have virtual wallet!"]]], 400); 
        }
        $wallet->balance = $wallet->balance + $request->value;
        $wallet->save();

         //Date/Time atual
        $date = Carbon::now();

        
        $movement->wallet_id = $wallet->id;
        $movement->type = "i";
        $movement->start_balance = $wallet->balance;
        $movement->end_balance = $wallet->balance + $request->value;
        $movement->transfer=0;
        $movement->date = $date->toDateTimeString();

        $movement->save();

       return response()->json(["id"=> $wallet->id, "email" => $wallet->email], 201);
    }
}
