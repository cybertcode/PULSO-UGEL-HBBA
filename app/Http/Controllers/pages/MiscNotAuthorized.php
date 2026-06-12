<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionInstitucional;

class MiscNotAuthorized extends Controller
{
  public function index()
  {
    $pageConfigs   = ['myLayout' => 'blank'];
    $configuracion = ConfiguracionInstitucional::cached();
    return view('content.pages.pages-misc-not-authorized', [
      'pageConfigs'   => $pageConfigs,
      'configuracion' => $configuracion,
    ]);
  }
}
