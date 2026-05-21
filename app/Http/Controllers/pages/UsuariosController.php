<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class UsuariosController extends Controller
{
  public function index()
  {
    return view('content.usuarios.index');
  }
}
