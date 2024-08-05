<?php

namespace App\Http\Controllers\registrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrarController extends Controller
{
    public function registrar_index()
    {
        return view('registrar.registrar_index');
    }
}
