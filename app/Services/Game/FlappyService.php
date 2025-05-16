<?php

namespace App\Services\Game;

use App\Models\Item\Item;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class FlappyService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Box Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of box type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData() {
        return [];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param mixed $game
     *
     * @return mixed
     */
    public function getGameData($game) {
        return $game->data;
    }

    /**
     * Processes the data attribute of the game and returns it in the preferred format.
     *
     * @param object $gameData
     * @param object $data
     *
     * @return bool
     */
    public function updateData($gameData, $data) {
        DB::beginTransaction();

        try {
            if (isset($data['player_image'])) {
                $this->handleImage($data['player_image'], 'gamefiles/flappy/assets', 'player.png');
            }
            if (isset($data['coin_image'])) {
                $this->handleImage($data['coin_image'], 'gamefiles/flappy/assets', 'coin.png');
            }
            if (isset($data['spikes_image'])) {
                $this->handleImage($data['spikes_image'], 'gamefiles/flappy/assets', 'spikes.png');
            }
            if (isset($data['background_image'])) {
                $this->handleImage($data['spikes_image'], 'gamefiles/flappy/assets', 'background.png');
            }

            $gameData->update(['data' => [
                'start_text' => $data['start_text'],
                'player_x'   => $data['player_x'],
                'player_y'   => $data['player_y'],
                'coin_x'     => $data['coin_x'],
                'coin_y'     => $data['coin_y'],
            ]]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
