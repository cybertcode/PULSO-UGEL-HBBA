<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AccessPermission extends Controller
{
    public function index()
    {
        $this->authorize('configuracion.ver');

        $permisos = Permission::withCount('roles')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($p) => explode('.', $p->name)[0]);

        return view('content.apps.app-access-permission', compact('permisos'));
    }
}
