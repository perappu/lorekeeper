<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Currency\Currency;
use App\Models\Game\Game;
use App\Models\Game\GameCategory;
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
            'categories' => [null => 'None'] + GameCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
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
            'categories'  => [null => 'None'] + GameCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'currencies'  => Currency::orderBy('name')->pluck('name', 'id'),
            'gameOptions' => $service->getGameOptions(),
        ] + (isset($game->data) ? $game->data->getEditData() : []));
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
            'game', 'game_data', 'category_id',
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

    /**
     * Sorts games.
     *
     * @param App\Services\GameService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortGame(Request $request, GameService $service) {
        if ($service->sortGame($request->get('sort'))) {
            flash('Game order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
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

    /************************************
     * GAME CATEGORIES
     * **********************************/

    /**
     * Shows the game index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCategoryIndex() {
        return view('admin.games.game_categories', [
            'categories' => GameCategory::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create game page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateGameCategory() {
        return view('admin.games.create_edit_game_category', [
            'category'       => new GameCategory(),
        ]);
    }

    /**
     * Shows the edit game page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditGameCategory(GameService $service, $id) {
        $category = GameCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('admin.games.create_edit_game_category', [
            'category'        => $category,
        ]);
    }

    /**
     * Creates or edits a game category.
     *
     * @param App\Services\GameService $service
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditGameCategory(Request $request, GameService $service, $id = null) {
        $data = $request->only([
            'name', 'description', 'image',
        ]);
        if ($id && $service->updateGameCategory(GameCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $game = $service->createGameCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();

            return redirect()->to('admin/data/game-categories/edit/'.$game->id);
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
    public function getDeleteGameCategory($id) {
        $cat = GameCategory::find($id);

        return view('admin.games._delete_game_category', [
            'category' => $cat,
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
    public function postDeleteGameCategory(Request $request, GameService $service, $id) {
        if ($id && $service->deleteGameCategory(Game::find($id))) {
            flash('Category deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/game-categories');
    }

    /**
     * Sorts games.
     *
     * @param App\Services\GameService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortGameCategory(Request $request, GameService $service) {
        if ($service->sortGameCategory($request->get('sort'))) {
            flash('Game category order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
