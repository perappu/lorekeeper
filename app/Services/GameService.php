<?php

namespace App\Services;

use App\Models\game\game;
use Illuminate\Support\Facades\DB;
use App\Services\GameFileManager;
use Log;

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

            //create the file directories
            $fileManager = new GameFileManager;
            $fileManager->createDirectory(public_path().'/'.$game->fileDirectory);
            $fileManager->createDirectory(public_path().'/'.$game->filesDirectory);

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

            if ($game) {
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
            $files = array_diff(scandir(public_path().$game->filesDirectory), ['.', '..']);
            if (count($files)) {
                throw new \Exception('Cannot delete a game with files. Delete the files and try again.');
            }

            $fileManager = new GameFileManager;
            $fileManager->deleteFile($game->htmlUrl);
            $fileManager->deleteDirectory($game->filesDirectory);
            $fileManager->deleteDirectory($game->fileDirectory);

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
