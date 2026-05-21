<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class ConfiguracionController extends Controller
{
  public function index()
  {
    return view('content.configuracion.index');
  }
}
