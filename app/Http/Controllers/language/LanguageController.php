<?php

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
  public function swap(Request $request, string $locale)
  {
    if (!in_array($locale, ['es', 'en'])) {
      abort(400);
    }
    $request->session()->put('locale', $locale);
    App::setLocale($locale);
    return redirect()->back();
  }
}