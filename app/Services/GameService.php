<?php

namespace App\Services;

use App\Models\Game\Game;
use App\Models\Game\GameCategory;
use App\Models\Game\GameData;
use Illuminate\Support\Facades\DB;

class GameService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Game Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of games.
    |
    */

    /**********************************************************************************************

        GAMES

    **********************************************************************************************/

    /**
     * Creates a new game.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\game\Game|bool
     */
    public function createGame($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateGameData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $game = Game::create($data);

            if ($image) {
                $this->handleImage($image, $game->gameImagePath, $game->gameImageFileName);
            }

            return $this->commitReturn($game);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a shop.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     * @param mixed                 $game
     *
     * @return \App\Models\game\Game|bool
     */
    public function updateGame($game, $data, $user) {
        DB::beginTransaction();

        try {
            // More specific validation
            if (Game::where('name', $data['name'])->where('id', '!=', $game->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $data = $this->populateGameData($data, $game);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $image = $data['image'];
                unset($data['image']);
            }

            $game->update($data);

            if ($image) {
                $this->handleImage($image, $game->gameImagePath, $game->gameImageFileName);
            }

            return $this->commitReturn($game);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a game.
     *
     * @param \App\Models\Game\Game $game
     *
     * @return bool
     */
    public function deleteGame($game) {
        DB::beginTransaction();

        try {
            //since data is tied to the tag rather than the game itself, any uploaded files are preserved
            //they will need to be manually removed/removed using the UI if available

            if ($game->has_image) {
                $this->deleteImage($game->gameImagePath, $game->gameImageFileName);
            }
            $game->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        GAME OPTIONS/DATA

    **********************************************************************************************/

    /**
     * Gets a list of game options for selection.
     *
     * @return array
     */
    public function getGameOptions() {
        $games = config('lorekeeper.game_options');
        $result = [];
        foreach ($games as $game => $gameData) {
            $result[$game] = $gameData['name'];
        }

        return $result;
    }

    /**
     * Adds game data tag to a game.
     *
     * @param \App\Models\Game\Game $game
     * @param mixed                 $user
     * @param mixed                 $gameOption
     *
     * @return bool|string
     */
    public function addGameData($game, $gameOption, $user) {
        DB::beginTransaction();

        try {
            if (!$game) {
                throw new \Exception('Invalid game selected.');
            }
            if ($game->data()->exists()) {
                throw new \Exception('This game already has data attached to it.');
            }
            if (!$gameOption) {
                throw new \Exception('No game data selected.');
            }

            if (!$this->logAdminAction($user, 'Added Game Data', 'Added '.$gameOption.' tag to '.$game->displayName)) {
                throw new \Exception('Failed to log admin action.');
            }

            $tag = GameData::create([
                'game_id'  => $game->id,
                'game'     => $gameOption,
            ]);

            return $this->commitReturn($tag);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Edits the data associated with an game.
     *
     * @param array $data
     * @param mixed $user
     * @param mixed $game
     *
     * @return bool|string
     */
    public function editGameData($game, $data, $user) {
        DB::beginTransaction();

        try {
            if (!$game) {
                throw new \Exception('Invalid game selected.');
            }
            if (!$game->data()->exists()) {
                throw new \Exception('This game does not have data attached to it.');
            }

            if (!$this->logAdminAction($user, 'Edited Game Data', 'Edited game data on '.$game->displayName)) {
                throw new \Exception('Failed to log admin action.');
            }

            $gameData = $game->data()->first();

            $service = $gameData->service;
            if (!$service->updateData($gameData, $data)) {
                $this->setErrors($service->errors());
                throw new \Exception('sdlfk... 2!');
            }

            return $this->commitReturn($gameData);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /********************
     * GAME CATEGORIES
     ********************/

    /**
     * Creates a new game.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\game\Game|bool
     */
    public function createGameCategory($data, $user) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            if (isset($data['description']) && $data['description']) {
                $data['parsed_description'] = parse($data['description']);
            } else {
                $data['parsed_description'] = null;
            }

            $category = GameCategory::create($data);

            if ($image) {
                $this->handleImage($image, $category->imagePath, $category->imageFileName);
            }

            return $this->commitReturn($category);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a shop.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     * @param mixed                 $category
     *
     * @return \App\Models\game\Game|bool
     */
    public function updateGameCategory($category, $data, $user) {
        DB::beginTransaction();

        try {
            // More specific validation
            if (GameCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $image = $data['image'];
                unset($data['image']);
            }

            if (isset($data['description']) && $data['description']) {
                $data['parsed_description'] = parse($data['description']);
            } else {
                $data['parsed_description'] = null;
            }

            $category->update($data);

            if ($image) {
                $this->handleImage($image, $category->imagePath, $category->imageFileName);
            }

            return $this->commitReturn($category);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a game.
     *
     * @param mixed $cat
     *
     * @return bool
     */
    public function deleteGameCategory($cat) {
        DB::beginTransaction();

        try {
            if ($cat->has_image) {
                $this->deleteImage($cat->imagePath, $cat->imageFileName);
            }
            $cat->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a game.
     *
     * @param array                 $data
     * @param \App\Models\game\Game $game
     *
     * @return array
     */
    private function populateGameData($data, $game = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['parsed_description'] = null;
        }
        $data['is_active'] = isset($data['is_active']);

        if (isset($data['remove_image'])) {
            if ($game && $game->has_image && $data['remove_image']) {
                $data['has_image'] = 0;
                $this->deleteImage($game->gameImagePath, $game->gameImageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }
}
