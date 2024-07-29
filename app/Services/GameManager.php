<?php

namespace App\Services;

use App\Models\Game\Game;
use App\Models\Game\GameScore;
use App\Models\Currency\Currency;
use App\Models\User\User;
use App\Services\CurrencyManager;
use Illuminate\Support\Facades\DB;
use Log;

class GameManager extends Service {

        /**
     * Processes score submission
     *
     * @param array                 $data
     * @param \App\Models\game\Game $game
     *
     * @return array
     */
    public function submitScore($data, $user) {

        DB::beginTransaction();

        try {
            
            $game = Game::where('id', $data['game_id'])->first();

            // increase the number of times we've played the game today by one
            if(GameScore::where('user_id', $data['user_id'])->where('game_id',$data['game_id'])->exists()) {

                $gameScore = GameScore::where('user_id', $data['user_id'])->where('game_id',$data['game_id'])->first();

                if ($gameScore->times_played >= $game->times_playable)
                    throw new \Exception("You've submitted the maximum number of plays today.");

                $data['times_played'] = $gameScore->times_played + 1;
                $gameScore->update($data);              

            } else {
                $data['times_played'] = 1;
                $gameScore = GameScore::create($data);
            }

            //grant currency
            $user = User::where('id', $data['user_id'])->first();
            $reward = ceil($data['score'] * $game->score_ratio);

            if ($reward >= $game->currency_cap)
                    $reward = $game->currency_cap;

            $currencyManager = new CurrencyManager;
            $currencyManager->creditCurrency(null, $user, 'Game Score', $game->id, $game->currency_id, $reward);

            return $this->commitReturn($gameScore);
            
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

}