<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class ModeloIntegridadController extends Controller
{
  public function index()
  {
    return view('content.modelo-integridad.index');
  }
}
