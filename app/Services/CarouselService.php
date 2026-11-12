<?php

namespace App\Services;

use App\Models\Carousel\Carousel;
use Illuminate\Support\Facades\DB;

class CarouselService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Carousel Service
    |--------------------------------------------------------------------------
    |
    | Handles uploading and manipulation of carousel files.
    |
    */

    /**
     * Creates a carousel.
     *
     * @param mixed $data
     * @param mixed $user
     *
     * @return bool
     */
    public function createCarousel($data, $user) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }

            $data['image'] = $image->getClientOriginalName();

            $carousel = Carousel::create($data);

            if (!$this->logAdminAction($user, 'Created carousel', 'Created '.$carousel->link)) {
                throw new \Exception('Failed to log admin action.');
            }

            if ($image) {
                $this->handleImage($image, $carousel->imagePath, $image->getClientOriginalName(), null);
            }

            return $this->commitReturn($carousel);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a carousel.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     * @param mixed                 $carousel
     *
     * @return bool|Carousel
     */
    public function updateCarousel($carousel, $data, $user) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
                $data['image'] = $image->getClientOriginalName();
            } else {
                unset($data['image']);
            }

            if (!isset($data['is_visible'])) {
                $data['is_visible'] = 0;
            }

            $carousel->update($data);

            if (!$this->logAdminAction($user, 'Updated carousel', 'Created '.$carousel->link)) {
                throw new \Exception('Failed to log admin action.');
            }

            if ($image) {
                $this->handleImage($image, $carousel->imagePath, $image->getClientOriginalName(), null);
            }

            return $this->commitReturn($carousel);
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
    public function deleteCarousel($carousel, $user) {
        DB::beginTransaction();

        try {
            $this->deleteImage($carousel->imagePath, $carousel->imageFileName);
            $carousel->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sorts carousel order.
     *
     * @param array $data
     *
     * @return bool
     */
    public function sortCarousel($data) {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach ($sort as $key => $s) {
                Carousel::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
