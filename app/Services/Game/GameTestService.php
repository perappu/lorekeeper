<?php

namespace App\Services\Game;

use App\Models\Item\Item;
use App\Services\InventoryManager;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class GameTestService extends Service {
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
     * @param object $tag
     *
     * @return mixed
     */
    public function getGameData($game) {

        return $game->data;
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param object $tag
     * @param array  $data
     *
     * @return bool
     */
    public function updateData($tag, $data) {
        DB::beginTransaction();

        try {
            $tag->update(['data' => $data]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

}
