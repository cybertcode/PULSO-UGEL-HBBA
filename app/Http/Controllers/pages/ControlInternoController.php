<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class ControlInternoController extends Controller
{
  public function index()
  {
    return view('content.control-interno.index');
  }
}
