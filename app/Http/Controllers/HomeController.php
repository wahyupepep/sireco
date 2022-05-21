<?php

namespace App\Http\Controllers;

use App\Models\{Category, Menu, User};
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categories = Category::count();
        $menus = Menu::count();
        $users = User::count();
        return view('home', compact('categories', 'menus', 'users'));
    }
}
