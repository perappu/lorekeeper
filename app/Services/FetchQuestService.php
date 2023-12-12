<?php namespace App\Services;

use App\Services\Service;

use DB;
use Notifications;
use Config;
use Settings;
use Auth;

use App\Services\InventoryManager;
use App\Services\CurrencyManager;

use App\Models\Item\Item;
use App\Models\Currency\Currency;

use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\User\UserCurrency;
use App\Models\FetchQuest\FetchQuest;
use App\Models\FetchQuest\FetchException;
use App\Models\FetchQuest\FetchLog;

class FetchQuestService extends Service
{
    /**
     * Attempts to complete the fetch quest.
     *
     * @param  array                        $data
     * @param  \App\Models\User\User        $user
     * @param  \App\Models\Item\UserItem    $stacks
     * @return bool
     */
    public function completeFetchQuest($data, $user, $id)
    {
        DB::beginTransaction();

        try {
            $fetch = FetchQuest::find($id);
            if (!$fetch) {
                abort(404);
            }

            $user = Auth::user();

            if ($user->fetchCooldown($fetch->id)) {
                throw new \Exception("You've completed this fetch quest too soon.");
            }

            $stack = UserItem::where([['user_id', $user->id], ['item_id', $fetch->fetchItem->id], ['count', '>', 0]])->first();

            if (!$stack) {
                throw new \Exception("You don't have the item to complete this quest.");
            }

            if (!(new InventoryManager())->debitStack($user, 'Turned in for Fetch Quest', ['data' => ''], $stack, 1)) {
                throw new \Exception('Failed to turn in quest.');
            }

            //successful turnin, so we credit the reward
            //first we randomize it though

            //check if all 4 currencies are set, if so, randomize the "current" value that was generated when it reset
            //if not, randomize normally
            if(isset($fetch->extras['reward_min_min']) &&
            isset($fetch->extras['reward_min_max']) &&
            isset($fetch->extras['reward_max_min']) &&
            isset($fetch->extras['reward_max_max']) &&
            $fetch->current_min &&
            $fetch->current_max &&
            $fetch->fetchCurrency && $fetch->fetchItem){
                $roll = mt_rand($fetch->current_min, $fetch->current_max - 1);
            }elseif(isset($fetch->extras['reward_min_min']) && isset($fetch->extras['reward_max_min']) && $fetch->fetchCurrency && $fetch->fetchItem){
                $roll = mt_rand($fetch->extras['reward_min_min'], $fetch->extras['reward_max_min'] - 1);
            }else{
                throw new \Exception('Currencies weren\'t set correctly for this fetch quest. Contact an admin.');
            }
            
            //credit now after the random shenanigans
            if (!(new CurrencyManager())->creditCurrency(null, $user, 'Fetch Quest Reward', 'Reward for completing fetch quest', $fetch->fetchCurrency, $roll)) {
                throw new \Exception('Failed to credit currency.');
            }
            if($fetch->questgiver_name){
                flash($fetch->questgiver_name.' generously pays you '.$roll.' '.$fetch->fetchCurrency->name.'.')->success();
            }else{
                flash('You earned '.$roll.' '.$fetch->fetchCurrency->name.'.')->success();
            }

            // make a log of the fetch action.
            $fetchLog = FetchLog::create([
                'fetch_id' => $fetch->id,
                'user_id' => $user->id,
                'item_id' => $fetch->fetch_item,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        FETCH CREATION

    **********************************************************************************************/

    /**
     * Creates a new fetch quest.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\FetchQuest\FetchQuest
     */
    public function createFetchQuest($data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $fetchquest = FetchQuest::create($data);
            $this->populateExceptions($data, $fetchquest);

            //add the rewards
            $fetchquest->update([
                'extras' => json_encode([
                    'reward_min_min' => isset($data['reward_min_min']) && $data['reward_min_min'] ? $data['reward_min_min'] : null,
                    'reward_min_max' => isset($data['reward_min_max']) && $data['reward_min_max'] ? $data['reward_min_max'] : null,
                    'reward_max_min' => isset($data['reward_max_min']) && $data['reward_max_min'] ? $data['reward_max_min'] : null,
                    'reward_max_max' => isset($data['reward_max_max']) && $data['reward_max_max'] ? $data['reward_max_max'] : null,
                ]),
            ]);

            if ($image) {
                $this->handleImage($image, $fetchquest->imagePath, $fetchquest->imageFileName);
            }

            return $this->commitReturn($fetchquest);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a fetchquest.
     *
     * @param  \App\Models\FetchQuest\FetchQuest  $FetchQuest
     * @param  array                      $data
     * @param  \App\Models\User\User      $user
     * @return bool|\App\Models\FetchQuest\FetchQuest
     */
    public function updateFetchQuest($fetchquest, $data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data, $fetchquest);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $fetchquest->update($data);
            $this->populateExceptions($data, $fetchquest);
            $fetchquest->update([
                'extras' => json_encode([
                    'reward_min_min' => isset($data['reward_min_min']) && $data['reward_min_min'] ? $data['reward_min_min'] : null,
                    'reward_min_max' => isset($data['reward_min_max']) && $data['reward_min_max'] ? $data['reward_min_max'] : null,
                    'reward_max_min' => isset($data['reward_max_min']) && $data['reward_max_min'] ? $data['reward_max_min'] : null,
                    'reward_max_max' => isset($data['reward_max_max']) && $data['reward_max_max'] ? $data['reward_max_max'] : null,
                ]),
            ]);

            if ($fetchquest) {
                $this->handleImage($image, $fetchquest->imagePath, $fetchquest->imageFileName);
            }

            return $this->commitReturn($fetchquest);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a fetchquest.
     *
     * @param  array                      $data
     * @param  \App\Models\FetchQuest\FetchQuest  $fetchquest
     * @return array
     */
    private function populateData($data, $fetchquest = null)
    {
        if (isset($data['fetch_category']) && $data['fetch_category'] == 'none') {
            $data['fetch_category'] = null;
        }

        if (isset($data['fetch_category']) && $data['fetch_category'] && !ItemCategory::where('id', $data['fetch_category'])->exists()) {
            throw new \Exception('The selected item category is invalid.');
        }

        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['parsed_description'] = null;
        }

        isset($data['is_active']) && $data['is_active'] ? $data['is_active'] : ($data['is_active'] = 0);

        if (isset($data['remove_image'])) {
            if ($fetchquest && $fetchquest->has_image && $data['remove_image']) {
                $data['has_image'] = 0;
                $this->deleteImage($fetchquest->imagePath, $fetchquest->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Deletes a fetchquest.
     *
     * @param  \App\Models\Prompt\Prompt  $fetchquest
     * @return bool
     */
    public function deleteFetchQuest($fetchquest)
    {
        DB::beginTransaction();

        try {
            if ($fetchquest->has_image) {
                $this->deleteImage($fetchquest->imagePath, $fetchquest->imageFileName);
            }

            DB::table('fetch_log')
                ->where('fetch_id', $fetchquest->id)
                ->delete();
            $fetchquest->exceptions->delete();
            $fetchquest->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Create the fetch quest exceptions list
     *
     * @param  \App\Models\Recipe\Recipe   $recipe
     * @param  array                       $data
     */
    private function populateExceptions($data, $fetch)
    {
        $fetch->exceptions()->delete();

        if (isset($data['exception_type'])) {
            foreach ($data['exception_type'] as $key => $type) {
                if (!isset($data['exception_id'][$key])) {
                    throw new \Exception('One of the exceptions was not specified.');
                }
                FetchException::create([
                    'fetch_quest_id' => $fetch->id,
                    'exception_type' => $type,
                    'exception_id' => $data['exception_id'][$key],
                ]);
            }
        }
    }
}
