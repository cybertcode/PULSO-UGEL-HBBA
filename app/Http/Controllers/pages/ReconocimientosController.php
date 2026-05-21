<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class ReconocimientosController extends Controller
{
  public function index()
  {
    return view('content.reconocimientos.index');
  }
}
