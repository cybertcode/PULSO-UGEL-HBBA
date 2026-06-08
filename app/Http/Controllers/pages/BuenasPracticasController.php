<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;

class BuenasPracticasController extends Controller
{
    public function index()
    {
        return view('content.buenas-practicas.index');
    }
}
