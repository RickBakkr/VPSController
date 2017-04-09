<?php

namespace VPSController\Http\Controllers;

use Illuminate\Http\Request,
    VPSController\Utilities\SolusVM,
    VPSController\Models\Server,
    Auth;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('home', ['servers' => Auth::user()->servers]);
    }
}
