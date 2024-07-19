<?php

namespace App\Http\Controllers;

use App\Models\Currency\Currency;
use App\Models\Game\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Shop Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing the shop index, shops and purchasing from shops.
    |
    */

    /**
     * Shows the game index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('games.index', [
            'games' => Game::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
        ]);
    }

       /**
     * Shows a shop.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGame($id) {
        $game = Game::where('id', $id)->where('is_active', 1)->first();
        if (!$game) {
            abort(404);
        }

        return view('games.game', [
            'game'  => $game,
            'games' => Game::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
        ]);
    }
}
