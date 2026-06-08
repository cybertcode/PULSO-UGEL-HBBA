<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class AyudaController extends Controller
{
    public function index()
    {
        return view('content.ayuda.index');
    }
}
