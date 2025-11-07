<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\House;

class HomeController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function home(){
        return view('home.index');
    }

    public function login_home(){
        $house = House::paginate(10);
        return view('home.index', compact('house'));
    }


    public function house_details($id)
    {
        $house = House::findOrFail($id);

        return view('home.house_details', compact('house'));
    }
    public function see_house(){
        $house = House::paginate(10);

        return view('home.see_house', compact('house'));
    }

}
