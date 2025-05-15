<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Currency\Currency;
use App\Models\Game\Game;
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
            'game'       => new Game,
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
    public function getEditGame(GameService $service, $id) {
        $game = Game::find($id);
        if (!$game) {
            abort(404);
        }

        return view('admin.games.edit_game', [
            'game'        => $game,
            'currencies'  => Currency::orderBy('name')->pluck('name', 'id'),
            'gameOptions' => $service->getGameOptions(),
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
            'name', 'description', 'image', 'remove_image', 'is_active', 
            'game_type', 'link',
            'currency_id', 'currency_cap', 'score_ratio', 'times_playable', 'playable_timeframe',
            'game','game_data'
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

    /**
     * Gets the game deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteGame($id) {
        $game = Game::find($id);

        return view('admin.games._delete_game', [
            'game' => $game,
        ]);
    }

    /**
     * Deletes a game.
     *
     * @param App\Services\GameService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteGame(Request $request, GameService $service, $id) {
        if ($id && $service->deleteGame(Game::find($id))) {
            flash('Game deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/games');
    }

    /********* GAME DATA HANDLING ***********/

    /**
     * Adds game data to a game.
     *
     * @param App\Services\GameService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddGameData(Request $request, GameService $service, $id) {
        $game = Game::find($id);
        $gameOption = $request->get('game');
        if ($gameData = $service->addGameData($game, $gameOption, Auth::user())) {
            flash('Game data added successfully.')->success();

            return redirect()->to($gameData->adminUrl);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Edits game data for a game.
     *
     * @param App\Services\GameService $service
     * @param int                      $id
     * @param string                   $tag
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditGameData(Request $request, GameService $service, $id) {
        $game = Game::find($id);
        if ($service->editGameData($game, $request->all(), Auth::user())) {
            flash('Game data edited successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

}
