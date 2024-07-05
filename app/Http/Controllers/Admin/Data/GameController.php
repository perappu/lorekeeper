<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Game\Game;
use App\Models\Currency\Currency;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Game Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of games.
    |
    */

    /**
     * Shows the game index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.games.games', [
            'games' => Game::orderBy('sort', 'DESC')->get(),
        ]);
    }

        /**
     * Shows the create game page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateGame() {
        return view('admin.games.create_game', [
            'game' => new Game,
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Shows the edit game page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditGame($id) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        return view('admin.games.edit_game', [
            'game'       => $game,
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
        ]);
    }

        /**
     * Creates or edits a game.
     *
     * @param App\Services\GameService $service
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditGame(Request $request, GameService $service, $id = null) {
        $id ? $request->validate(Game::$updateRules) : $request->validate(Game::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_active', 'currency_id', 'currency_cap', 'score_ratio'
        ]);
        if ($id && $service->updateGame(Game::find($id), $data, Auth::user())) {
            flash('Game updated successfully.')->success();
        } elseif (!$id && $game = $service->createGame($data, Auth::user())) {
            flash('Game created successfully.')->success();

            return redirect()->to('admin/data/games/edit/'.$game->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

}