<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionInstitucional;

class LoginCover extends Controller
{
  public function index()
  {
    $pageConfigs   = ['myLayout' => 'blank'];
    $configInstit  = ConfiguracionInstitucional::first();
    return view('content.authentications.auth-login-cover', compact('pageConfigs', 'configInstit'));
  }
}
