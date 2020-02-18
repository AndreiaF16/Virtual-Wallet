<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model{
    //
    protected $fillable = [
        'id',
        'email',
        'balance',
    ];

    protected $hidden = [
    ];
     public function user()
     {
         return $this->belongsTo(User::class);
     }
     
     public function movement()
     {
        return $this->hasMany('App\Movement','wallet_id','id');
        //return $this->belongsTo(Movement::class);
     }
  /*  public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }*/

}


