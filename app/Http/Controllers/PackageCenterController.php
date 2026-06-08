<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PackageCenterController extends Controller
{
    public function __invoke(): View
    {
        return view('package-center.index');
    }
}
