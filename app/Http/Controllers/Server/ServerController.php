<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class ServerController extends Controller
{
    public function index()
    {
        return view('admin.server.index');
    }

    public function horizonClear()
    {
        Artisan::call('horizon:clear');
        return redirect()->back()->with('status', 'Pending jobs cleared!');
    }

    public function horizonRestart()
    {
        Artisan::call('horizon:terminate');
        return redirect()->back()->with('status', 'Supervisor restarted!');
    }
}
