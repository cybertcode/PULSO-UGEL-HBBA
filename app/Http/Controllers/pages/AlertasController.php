<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class AlertasController extends Controller
{
  public function index()
  {
    return view('content.alertas.index');
  }
}
