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
        $canViewDashboard = $user && $user->canAccessPage('dashboard', 'view');

        return view('home', compact('homeCategories', 'canViewDashboard'));
    }
}
