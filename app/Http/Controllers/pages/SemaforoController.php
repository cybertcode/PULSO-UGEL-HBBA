<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class SemaforoController extends Controller
{
  public function index()
  {
    return view('content.semaforo.index');
  }
}
