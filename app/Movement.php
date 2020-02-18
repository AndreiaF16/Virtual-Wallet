<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Movement extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $timestamps = false;
    protected $fillable = [
        'wallet_id','type','transfer','transfer_movement_id','transfer_wallet_id','type_payment','category_id',
        'iban','mb_entity_code','mb_payment_reference','source_description','date','start_balance','end_balance' ,'value'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //'password', 'remember_token',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function category()
    {
        return $this->hasOne(Category::class,"id","category_id");
    }

   public function transfer_wallet() //atenção ao create credit!
    {
        return $this->hasOne(Wallet::class,  'id', 'transfer_wallet_id');

    }
}
