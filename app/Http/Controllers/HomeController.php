<?php

namespace App\Http\Controllers;

use App\Support\HomeMenu;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $homeCategories = HomeMenu::categoriesForUser($user);

        return view('home', compact('homeCategories'));
    }
}
