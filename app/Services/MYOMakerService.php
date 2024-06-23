<?php

namespace App\Services;

use App\Models\MYOMaker\MYOMakerCategory;
use Illuminate\Support\Facades\DB;

class MYOMakerService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Item Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of item categories and items.
    |
    */

    /**********************************************************************************************

        ITEM CATEGORIES

    **********************************************************************************************/

    /**
     * Create a category.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Item\ItemCategory|bool
     */
    public function createCategory($data, $user) {
        DB::beginTransaction();

        try {
            $category = MYOMakerCategory::create($data);

            return $this->commitReturn($category);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

}