<?php

namespace App\Http\Controllers;

use App\Models\Game\Game;
use App\Services\GameManager;
use App\Services\GameService;
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

    public function postSubmitScore(Request $request, GameManager $service) {

        if ($service->submitScore($request->only(['user_id','game_id','score']), Auth::user())) {
            
            flash('Score submitted and currency rewarded.')->success();

        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
