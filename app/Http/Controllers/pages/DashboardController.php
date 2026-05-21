<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
  public function index()
  {
    return view('content.dashboard.index');
  }
}
