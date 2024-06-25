<?php

namespace App\Services;

use App\Models\RandomGenerator\RandomCategory;
use App\Models\RandomGenerator\RandomObject;
use Illuminate\Support\Facades\DB;

class RandomGeneratorService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Random Generator Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of random generator categories and objects.
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
     * @return \App\Models\Item\RandomCategory|bool
     */
    public function createRandomCategory($data, $user) {
        DB::beginTransaction();

        try {
            $category = RandomCategory::create($data);

            return $this->commitReturn($category);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Update a category.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Item\RandomCategory|bool
     */
    public function updateRandomCategory($category, $data, $user) {
        DB::beginTransaction();

        try {

            if (RandomCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $category->update($data);

            return $this->commitReturn($category);
            
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a category.
     *
     * @param mixed $carousel
     * @param mixed $user
     *
     * @return bool
     */
    public function deleteRandomCategory($category, $user) {
        DB::beginTransaction();

        try {
            $category->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /*********** IMAGES */

        /**
     * Uploads a file.
     *
     * @param mixed $data
     * @param mixed $user
     *
     * @return \App\Models\Item\MYOMakerImage|bool
     */
    public function updateMYOMakerImage($myomakerimage, $data, $user) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }

            $data['image'] = $data['category_id'].'_'.$data['id'];


            if ($image) {
                $this->handleImage($image, $myomakerimage->imagePath, $data['image'], null);
            }

            $myomakerimage->update($data);

            return $this->commitReturn($myomakerimage);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

            /**
     * Uploads a file.
     *
     * @param mixed $data
     * @param mixed $user
     *
     * @return bool
     */
    public function createMYOMakerImage($data, $user) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }

            $hash = randomString(10);

            $data['image'] = $data['category_id'].'_'.$hash.'.png';

            $myomakerimage = RandomObject::create($data);

            if (!$this->logAdminAction($user, 'Created myo maker image', 'Created '.$myomakerimage->link)) {
                throw new \Exception('Failed to log admin action.');
            }

            if ($image) {
                $this->handleImage($image, $myomakerimage->imagePath, $data['image'], null);
            }

            return $this->commitReturn($myomakerimage);

        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a file.
     *
     * @param mixed $carousel
     * @param mixed $user
     *
     * @return bool
     */
    public function deleteMYOMakerImage($myomakerimage, $user) {
        DB::beginTransaction();

        try {
            $this->deleteImage($myomakerimage->imagePath, $myomakerimage->imageFileName);
            $myomakerimage->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

}