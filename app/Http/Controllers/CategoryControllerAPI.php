<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\Http\Resources\Category as CategoryResource;
use App\User;
use Illuminate\Support\Facades\Auth;

class CategoryControllerAPI extends Controller
{
    public function CategoriesExpense()
    {
        return CategoryResource::collection(Category::where("type","e")->get());
    }

    public function CategoriesIncome()
    {
        return CategoryResource::collection(Category::where("type","i")->get());
    }

    public function Categories()
    {
        return CategoryResource::collection(Category::all());
    }

    public function categoryName(){
        $categories= Category::distinct('name')->get();
        return $categories;
    }
}