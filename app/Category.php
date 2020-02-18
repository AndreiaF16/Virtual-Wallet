<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name'
    ];

    public function movements()
    {
        return $this->hasMany('App\Movement', 'category_id', 'id');
    }
}