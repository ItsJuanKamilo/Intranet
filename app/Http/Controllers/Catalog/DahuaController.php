<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;

class DahuaController extends Controller
{
    public function index()
    {
        return view('dahua.index'); // Asegúrate de que la vista exista en resources/views/dahua/index.blade.php
    }
}
