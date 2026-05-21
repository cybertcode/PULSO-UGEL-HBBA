<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class EvidenciasController extends Controller
{
  public function index()
  {
    return view('content.evidencias.index');
  }
}
