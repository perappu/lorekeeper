<?php

namespace App\Services;

use App\Models\Currency\Currency;
use App\Models\Game\Game;
use App\Models\Game\GameScore;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;

class GameManager extends Service {
    /**
     * Checks if a user has hit their playable cap or not.
     *
     * @param mixed $gameId
     * @param mixed $userId
     *
     * @return bool
     */
    public function canSubmitScore($gameId, $userId) {
        $game = Game::find($gameId);
        if (!$game) {
            throw new \Exception('Game not found');
        }

        $score = GameScore::where('user_id', $userId)->where('game_id', $gameId)->first();

        if ($score->times_played >= $game->times_playable) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Processes score submission.
     *
     * @param array $data
     * @param mixed $user
     *
     * @return array
     */
    public function submitScore($data, $user) {
        DB::beginTransaction();

        try {
            $game = Game::where('id', $data['game_id'])->first();

            $score = GameScore::where('user_id', $data['user_id'])->where('game_id', $data['game_id']);

            // increase the number of times we've played the game today by one
            // scores will be reset every day by the kernel command, so we don't need to handle it here
            if ($score->exists()) {
                $gameScore = $score->first();

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

                if ($gameScore->times_played >= $game->times_playable) {
                    throw new \Exception("You've submitted the maximum number of plays for ".$timeframe.'.');
                }

                $data['times_played'] = $gameScore->times_played + 1;
                $data['high_score'] = $data['score'] > $gameScore->high_score ? $data['score'] : $gameScore->high_score;

                $gameScore->update($data);
            } else {
                $data['times_played'] = 1;
                $gameScore = GameScore::create($data);
            }

            //grant currency
            $user = User::where('id', $data['user_id'])->first();
            $reward = ceil($data['score'] * $game->score_ratio);

            if ($reward >= $game->currency_cap) {
                $reward = $game->currency_cap;
            }

            $currencyManager = new CurrencyManager;
            $currencyManager->creditCurrency(null, $user, 'Game Reward', $game->displayName, $game->currency_id, $reward);

            return $this->commitReturn($reward);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Subtracts currency amount, for betting games and things like that.
     *
     * @param array $data
     * @param mixed $user
     *
     * @return array
     */
    public function chargeCurrency($data, $user) {
        DB::beginTransaction();

        try {
            $game = Game::where('id', $data['game_id'])->first();

            $currencyManager = new CurrencyManager;
            //this will ALWAYS subtract the number given, even if it's initially positive. no player circumventing!
            $currencyManager->creditCurrency(null, $user, 'Game Payment', $game->displayName, $data['currency_id'], -1 * abs($data['amount']));

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Gets the game data based on id.
     *
     * @param mixed $user
     * @param mixed $id
     *
     * @return array
     */
    public function postGameData($id, $user) {
        $game = Game::where('id', $id)->first();

        return $game->data->data;
    }
}
