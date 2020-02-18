<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User as UserResource;
use App\Http\Controllers\Requests\RegisterUserRequest;
use App\Http\Controllers\Requests\UpdateUserRequest;
use App\Http\Controllers\Requests\RegisterUserAdminOpRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Support\Jsonable;
use \DateTime;

define('SOURCE_EMAIL', env('MAIL_USERNAME'));

class UserControllerAPI extends Controller
{
    public function getAuthUser(){
        return new UserResource(Auth::user());
    }

    public function getUser($email){
        $user = User::where("email","=",$email)->first();
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request)
    {
        $user = Auth::user();    

        $user->fill($request->except(['file']));

        if ($request->hasFile('file') && $request->file('file')->isValid()) {

            $fileName = $user->id . '_' . $request->file('file')->hashName();
            $request->file('file')->storeAs('public/fotos', $fileName);
            $user->fill(['photo' => $fileName,]);
        }

        $user->save();

        return response()->json(new UserResource($user), 201);
    }

    public function changePassword(Request $request){

       $validated = $request->validate([
            'password_old'=>'required|min:3',
            'password'=>'required|confirmed|min:3|different:password_old',
            'password_confirmation'=>'required|same:password',
       ], [
            'password_old.required' => 'Is necessary insert the old password',
            'password.required' => 'Is necessary insert the new password',
            'password_confirmation.required' => 'Is necessary confirm the new password',
            'password_old.min' => 'The old password needs at least 3 characters',
            'password.min' => 'The new password needs at least 3 characters',
            'password_confirmation.min' => 'Password confirmation must be at least 3 characters',
            'password_confirmation.same' => 'Verification password must match password'
        ]);
        $user = Auth::user();

      //  $user->fill($validated);

        if (!(Hash::check($request->input('password_old'), $user->password))) {
            return response()->json([
                'password_old' => 'Please enter the correct current password'
            ], 422);
        }


        $user->password=Hash::make($request->input('password'));

        $user->save();

        return new UserResource($user);
    }
    public function store(RegisterUserAdminOpRequest $request) {
        $user = new User([
            'name' => $request['name'],
            'type' => $request ['type'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {

            $fileName = $user->id . '_' . $request->file('photo')->hashName();
            $request->file('photo')->storeAs('public/fotos', $fileName);
            $user->fill(['photo' => $fileName,]);
        }
        $user->save();
        return new UserResource($user);
    }

    public function deactivateUser($id){
        $user = User::findOrFail($id);
        $balance = DB::table('wallets')->select('balance')->where('id', $id)->get();
        if($balance[0]->balance == 0){
            $user->active = 0;
            $user->save();
        }else{
            return "Wallet balance must be 0.00 to deactivate a user!";
        }
        return new UserResource($user);
    }

    public function activateUser($id){
        $user = User::findOrFail($id);
        $user->active = 1;
        $user->save();
        return new UserResource($user);
    }

    public function getFilteredUsers(Request $request){

        if(!is_null($request->name) || !is_null($request->type) || !is_null($request->email) || !is_null($request->active)){

            $users = User::with('wallet')->select('*');

            if(!is_null($request->name)){
                $users = $users->where('name', 'like', $request->name . '%');
            }
            if(!is_null($request->type)){
                $users = $users->where('type', $request->type);
            }
            if(!is_null($request->email)){
                $users = $users->where('email', 'like', $request->email . '%');
            }
            if(!is_null($request->active)){
                if($request->type == null || $request->type == 'u'){
                    $users = $users->where('active', $request->active);
                }else{
                    return "Can't search by status and type at the sime time, because the type of user is different from 'Platform User'!";
                }
            }

            $users = $users->paginate(10);

        }else{
            $users = User::with('wallet')->select('*')->paginate(10);
        }
        return $users;
    }

    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function getPhotoByEmail($email){
        $photo = User::where('email', '=', $email)->pluck('photo');
        return $photo;
    }
    
    public function sendEmail(){
        Mail::send([], [], function ($message) {
            $message->to(request()->email)
                ->subject('New movement in your account!')
                ->from(SOURCE_EMAIL, 'Virtual Wallet')
                ->setBody(request()->msg);
        });

        return response()->json(['message' => 'Email sent!']);
    }
    public function allUsers(){
        
        $users = User::all();
        return $users;
    }
    public function totalUsers()
    {
        $data = DB::table('users')->count();
        return $data;
    }
    public function totalOperators()
    {
        $data = DB::table('users')
            ->where('type', 'LIKE', 'O')
            ->count();
        return $data;
    }
    public function totalPlatformUsers()
    {
        $data = DB::table('users')
            ->where('type', 'LIKE', 'u')
            ->count();
        return $data;
    }
    public function totalAdmins()
    {
        $data = DB::table('users')
            ->where('type', 'LIKE', 'a')
            ->count();
        return $data;
    }

    public function getNumberActiveUsers(){
        $data = DB::table('users')->where('type', 'u')->where('active', '1')->count();       
        
        return $data;
    }
    public function getTotalTransactions(){
        $data = DB::table('movements')->where('transfer', '1')->count();
        return $data;
       
    }
    public function getTotalAmmountMoney(){
        $wallets = DB::table('wallets')->select('balance')->get();
        $sizeWallets = count($wallets);
        $totalBalance = 0;
        for($i = 0; $i < $sizeWallets; $i++){
            $totalBalance = $totalBalance + $wallets[$i]->balance;
        }
        $data = number_format((float)$totalBalance, 2, '.', '');
        return $data;
    }
    public function getMovementsThroughTime(){
        $time = strtotime("-1 year", time());
        $date = date("Y-m-d", $time);
        $movements = DB::table('movements')->select('date')->whereDate("date", ">=" ,$date)->orderBy('date', 'asc')->get();
        $firstYear = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[0]->date)->format("Y");
        $firstMonth = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[0]->date)->format("m");
        $lastYear = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[count($movements)-1]->date)->format("Y");
        $lastMonth = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[count($movements)-1]->date)->format("m");
        $count = 0;

        for ($i=$firstYear; $i <= $lastYear; $i++){ 
            if($count == 0){
                
                for ($j=$firstMonth; $j < $lastMonth+12; $j++){         
                    if($j < 10){
                        $m = "0".intval($j);
                    }else{
                        $m = intval($j);
                    }
                    
                    $movementsMonth = DB::table('movements')->where('date', 'like', $i . '-' . $m . '%')->orderBy('date', 'asc')->count();
                    $totalMevementsMonth[$count]['value'] = $movementsMonth;
                    $totalMevementsMonth[$count]['date'] = $i.'-'.$m;
                    
                    $count = $count + 1;
                }
            }else{
                $firstMonth = 1;
                for ($j=$firstMonth; $j < $lastMonth+1; $j++){  
                    if($j < 10){
                        $m = "0".intval($j);
                    }else{
                        $m = intval($j);
                    }
                    $movementsMonth = DB::table('movements')->where('date', 'like', $i . '-' . $m . '%')->orderBy('date', 'asc')->count();
                    $totalMevementsMonth[$count]['value'] = $movementsMonth;
                    $totalMevementsMonth[$count]['date'] = $i.'-'.$m;
                    
                    
                    $count = $count + 1;
                }
            }
        }
        
        $total = count($totalMevementsMonth);
        for($i = 0; $i < $total; $i++){
            $labels[] = $totalMevementsMonth[$i]['date'];
            $rows[] = $totalMevementsMonth[$i]['value'];
        }
        $data = [
            'labels' => $labels,
            'rows' => $rows,
        ];
        return response()->json($data, 200);
    }
    public function getExternalIncomeThroughTimeThroughTime(){
        $time = strtotime("-1 year", time());
        $date = date("Y-m-d", $time);
        $movements = DB::table('movements')->select('date')->where('type', 'i')->where('transfer', '0')->whereDate("date", ">=" ,$date)->orderBy('date', 'asc')->get();
        $firstYear = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[0]->date)->format("Y");
        $firstMonth = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[0]->date)->format("m");
        $lastYear = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[count($movements)-1]->date)->format("Y");
        $lastMonth = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[count($movements)-1]->date)->format("m");
        
        $count = 0;
        for ($i=$firstYear; $i <= $lastYear; $i++){ 
            if($count == 0){
                for ($j=$firstMonth; $j < 12; $j++){
                    if($j < 10){
                        $m = "0".intval($j);
                    }else{
                        $m = intval($j);
                    }  
                    $externalIncomesMonth = DB::table('movements')->where('type', 'i')->where('transfer', '0')->where('date', 'like', $i . '-' . $m . '%')->orderBy('date', 'asc')->count();
                    $totalExternalIncomesMonth[$count]['value'] = $externalIncomesMonth;
                    $totalExternalIncomesMonth[$count]['date'] = $i.'-'.$m;
                    
                    
                    
                    $count = $count + 1;
                }
            }else{
                $firstMonth = 1;
                for ($j=$firstMonth; $j < $lastMonth+1; $j++){ 
                    if($j < 10){
                        $m = "0".intval($j);
                    }else{
                        $m = intval($j);
                    }

                    $externalIncomesMonth = DB::table('movements')->where('type', 'i')->where('transfer', '0')->where('date', 'like', $i . '-' . $m . '%')->orderBy('date', 'asc')->count();
                    $totalExternalIncomesMonth[$count]['value'] = $externalIncomesMonth;
                    $totalExternalIncomesMonth[$count]['date'] = $i.'-'.$m;
                    
                    
                    $count = $count + 1;
                }
            }
        }
       
        $total = count($totalExternalIncomesMonth);
        for($i = 0; $i < $total; $i++){
            $labels[] = $totalExternalIncomesMonth[$i]['date'];
            $rows[] = $totalExternalIncomesMonth[$i]['value'];
        }
        $data = [
            'labels' => $labels,
            'rows' => $rows,
        ];
        return response()->json($data, 200);
    }
    public function getInternalTransfersThroughTimeThroughTime(){
        $time = strtotime("-1 year", time());
        $date = date("Y-m-d", $time);
        $movements = DB::table('movements')->select('date')->where('type', 'e')->where('transfer', '1')->whereDate("date", ">=" ,$date)->orderBy('date', 'asc')->get();
        $firstYear = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[0]->date)->format("Y");
        $firstMonth = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[0]->date)->format("m");
        $lastYear = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[count($movements)-1]->date)->format("Y");
        $lastMonth = DateTime::createFromFormat('Y-m-d H:i:s' ,$movements[count($movements)-1]->date)->format("m");
        $count = 0;
        for ($i=$firstYear; $i <= $lastYear; $i++){ 
            if($count == 0){
                for ($j=$firstMonth; $j < 12; $j++){    
                    if($j < 10){
                        $m = "0".intval($j);
                    }else{
                        $m = intval($j);
                    }
                    $transfersMonth = DB::table('movements')->where('type', 'e')->where('transfer', '1')->where('date', 'like', $i . '-' . $m . '%')->orderBy('date', 'asc')->count();
                        $totalTransfersMonth[$count]['value'] = $transfersMonth;
                        $totalTransfersMonth[$count]['date'] = $i.'-'.$m;
                    
                    $count = $count + 1;
                }
            }else{
                $firstMonth = 1;
                for ($j=$firstMonth; $j < $lastMonth+1; $j++){  
                    if($j < 10){
                        $m = "0".intval($j);
                    }else{
                        $m = intval($j);
                    }            
                    $transfersMonth = DB::table('movements')->where('type', 'e')->where('transfer', '1')->where('date', 'like', $i . '-' . $m . '%')->orderBy('date', 'asc')->count();
                    
                    $totalTransfersMonth[$count]['value'] = $transfersMonth;
                    $totalTransfersMonth[$count]['date'] = $i.'-'.$m;
                    
                    $count = $count + 1;
                }
            }
        }
        $total = count($totalTransfersMonth);
        for($i = 0; $i < $total; $i++){
            $labels[] = $totalTransfersMonth[$i]['date'];
            $rows[] = $totalTransfersMonth[$i]['value'];
        }
        $data = [
            'labels' => $labels,
            'rows' => $rows,
        ];
        return response()->json($data, 200);
    }
    public function getUsersRegisteredThroughTime(){
        $time = strtotime("-1 year", time());
        $date = date("Y-m-d", $time);
        $users = DB::table('users')->select('created_at')->where('type', 'u')->orderBy('created_at', 'asc')->whereDate("created_at", ">=" ,$date)->get();
        $firstYear = DateTime::createFromFormat('Y-m-d H:i:s' ,$users[0]->created_at)->format("Y");
        $firstMonth = DateTime::createFromFormat('Y-m-d H:i:s' ,$users[0]->created_at)->format("m");
        $lastYear = DateTime::createFromFormat('Y-m-d H:i:s' ,$users[count($users)-1]->created_at)->format("Y");
        $lastMonth = DateTime::createFromFormat('Y-m-d H:i:s' ,$users[count($users)-1]->created_at)->format("m");
        $count = 0;
        for ($i=$firstYear; $i <= $lastYear; $i++){ 
            if($count == 0){
                for ($j=$firstMonth; $j < 12; $j++){
                    if($j < 10){
                        $m = "0".intval($j);
                    }else{
                        $m = intval($j);
                    }      
                    $usersMonth = DB::table('users')->where('type', 'u')->where('created_at', 'like', $i . '-' . $m . '%')->orderBy('date', 'asc')->count();
                   
                    $totalUsersMonth[$count]['value'] = $usersMonth;
                    $totalUsersMonth[$count]['date'] = $i.'-'.$m;
                    
                    $count = $count + 1;
                }
            }else{
                $firstMonth = 1;
                for ($j=$firstMonth; $j < $lastMonth+1; $j++){  
                    if($j < 10){
                        $m = "0".intval($j);
                    }else{
                        $m = intval($j);
                    }            
                    $usersMonth = DB::table('users')->where('type', 'u')->where('created_at', 'like', $i . '-' . $m . '%')->orderBy('date', 'asc')->count();
                    
                    $totalUsersMonth[$count]['value'] = $usersMonth;
                    $totalUsersMonth[$count]['date'] = $i.'-'.$m;
                    
                    $count = $count + 1;
                }
            }
        }
     
        $total = count($totalUsersMonth);
        for($i = 0; $i < $total; $i++){
            $labels[] = $totalUsersMonth[$i]['date'];
            $rows[] = $totalUsersMonth[$i]['value'];
        }
        $data = [
            'labels' => $labels,
            'rows' => $rows,
        ];
        return response()->json($data, 200);
    }
    

    
}
