<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportProblemsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request)
    {
        return view('pages.report_problems');
    }
}
