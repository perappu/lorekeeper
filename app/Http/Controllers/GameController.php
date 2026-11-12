<?php

namespace App\Http\Controllers;

use App\Models\Game\Game;
use App\Models\Game\GameCategory;
use App\Models\Game\GameScore;
use App\Services\GameManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Game Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing the game index, games and submitting scores.
    |
    */

    /**
     * Shows the game index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('games.index', [
            'games'      => Game::with('category')->where('is_active', 1)->orderBy('sort', 'DESC')->get()->groupBy('category.id'),
            'categories' => GameCategory::all()->keyBy('id'),
        ]);
    }

    /**
     * Shows a game.
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

        switch ($game->playable_timeframe) {
            case 'daily':
                $timeframe = 'today';
                break;
            case 'weekly':
                $timeframe = 'this week';
                break;
            case 'monthly':
                $timeframe = 'this month';
                break;
            default:
                $timeframe = '';
                break;
        }

        return view('games.game', [
            'game'       => $game,
            'gameScore'  => GameScore::where('user_id', Auth::user()->id)->where('game_id', $id)->first() ?? null,
            'games'      => Game::with('category')->where('is_active', 1)->orderBy('sort', 'DESC')->get()->groupBy('category.id'),
            'categories' => GameCategory::all()->keyBy('id'),
            'timeframe'  => $timeframe,
        ]);
    }

    /** API-esque functions that games can call to handle scoring and charging **/

    /**
     * Submits a score.
     *
     * @param Illuminate\Http\Request  $request
     * @param App\Services\GameManager $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postSubmitScore(Request $request, GameManager $service) {
        if ($reward = $service->submitScore($request->only(['user_id', 'game_id', 'score']), Auth::user())) {
            return response()->json(['submitted' => true, 'reward' => $reward]);
        } else {
            return response()->json(['submitted' => false, 'reward' => null, 'errors' => $service->errors()]);
        }
    }

    /**
     * Checks if a user can submit a score.
     *
     * @param Illuminate\Http\Request  $request
     * @param App\Services\GameManager $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCanSubmitScore(Request $request, GameManager $service) {
        if ($canSubmit = $service->canSubmitScore($request->input('game_id'), $request->input('user_id'))) {
            return response()->json(['can_submit' => $canSubmit]);
        } else {
            return response()->json(['can_submit' => false, 'errors' => $service->errors()]);
        }
    }

    /**
     * Charges a user currency.
     *
     * @param Illuminate\Http\Request  $request
     * @param App\Services\GameManager $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postChargeCurrency(Request $request, GameManager $service) {
        if ($service->chargeCurrency($request->only(['user_id', 'game_id', 'currency_id', 'amount']), Auth::user())) {
            return response()->json(['successful' => true]);
        } else {
            return response()->json(['successful' => false, 'errors' => $service->errors()]);
        }
    }

    /**
     * Retrieves game data based on given ID
     * This is a post request because it's harder for users to just get the data if it's a post request.
     *
     * @param Illuminate\Http\Request  $request
     * @param App\Services\GameManager $service
     * @param mixed                    $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postGameData(Request $request, GameManager $service, $id) {
        if ($data = $service->postGameData($id, Auth::user())) {
            return response()->json(['successful' => true, 'data' => $data]);
        } else {
            return response()->json(['successful' => false, 'errors' => $service->errors()]);
        }
    }

    /*********************************************************************
     ******** FUNCTIONS FOR SPECIFIC GAMES BELOW THIS COMMENT ************
     *********************************************************************/
}
