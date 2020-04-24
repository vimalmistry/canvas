<?php

namespace Canvas\Http\Controllers;

use Canvas\Canvas;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    /**
     * Return the SPA with global variables.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('canvas::layout', [
            'assetsUpToDate' => Canvas::assetsUpToDate(),
            'scripts'        => Canvas::scriptVariables(),
            'stylesheet'     => Canvas::$useDarkMode ? 'app-dark.css' : 'app.css',
        ]);
    }
}
