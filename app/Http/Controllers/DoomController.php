<?php

namespace App\Http\Controllers;


class DoomController extends Controller {

    /**
     * Creates a new controller instance.
     */
    /**
     * Shows the homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGame() {

        return view('doom', []);
    }
}
