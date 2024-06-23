<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Item\ItemCategory;
use App\Models\MYOMaker\MYOMakerCategory;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\MYOMaker\MYOMakerImage;
use App\Services\MYOMakerService;

class MYOMakerController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / MYO Maker Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of myo maker categories and images.
    |
    */

    /******** IMAGES */

    /**
     * Shows the image index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {

        return view('admin.myomaker.index', [
            'images' => MYOMakerImage::orderBy('category_id', 'DESC')->get(),
        ]);
    }

        /**
     * Shows the create category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateMYOMakerImage() {
        return view('admin.myomaker.create_edit_image', [
            'image' => new MYOMakerImage,
            'categories'     => ['none' => 'No category'] + MYOMakerCategory::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

        /**
     * Shows the edit item category page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditMYOMakerImage($id) {
        $image = MYOMakerImage::find($id);
        if (!$image) {
            abort(404);
        }

        return view('admin.myomaker.create_edit_image', [
            'image' => $image,
            'categories'     => ['none' => 'No category'] + MYOMakerCategory::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

        /**
     * Gets the item category deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteMYOMakerImage($id) {
        $image = MYOMakerImage::find($id);
        if (!$image) {
            abort(404);
        }

        return view('admin.myomaker._delete_image', [
            'image' => $image,
        ]);
    }

        /**
     * Uploads a file.
     *
     * @param mixed $data
     * @param mixed $user
     *
     * @return bool
     */
    public function postCreateEditMYOMakerImage(Request $request, MYOMakerService $service, $id = null) {
        $id ? $request->validate(MYOMakerImage::$updateRules) : $request->validate(MYOMakerImage::$createRules);
        $data = $request->only([
            'name', 'image', 'category_id'
        ]);
        if ($id && $service->updateMYOMakerImage(MYOMakerImage::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $category = $service->createMYOMakerImage($data, Auth::user())) {
            flash('Category created successfully.')->success();

            return redirect()->to('admin/data/myomaker/edit/'.$category->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Deletes an item category.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteMYOMakerImage(Request $request, MYOMakerService $service, $id) {
        if ($id && $service->deleteMYOMakerImage(MYOMakerImage::find($id), Auth::user())) {
            flash('Image deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/myomaker');
    }


    /******** CATEGORIES */

    /**
     * Shows the category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMYOMakerCategoryIndex() {

        return view('admin.myomaker.categories', [
            'categories' => MYOMakerCategory::orderBy('name', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateMYOMakerCategory() {
        return view('admin.myomaker.create_edit_category', [
            'category' => new MYOMakerCategory,
        ]);
    }

    /**
     * Shows the edit item category page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditMYOMakerCategory($id) {
        $category = MYOMakerCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('admin.myomaker.create_edit_category', [
            'category' => $category,
        ]);
    }

    /**
     * Creates or edits a category.
     *
     * @param App\Services\ItemService $service
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditMYOMakerCategory(Request $request, MYOMakerService $service, $id = null) {
        $id ? $request->validate(MYOMakerCategory::$updateRules) : $request->validate(MYOMakerCategory::$createRules);
        $data = $request->only([
            'name'
        ]);
        if ($id && $service->updateMYOMakerCategory(MYOMakerCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $category = $service->createMYOMakerCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();

            return redirect()->to('admin/data/myomaker/category/edit/'.$category->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the item category deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteMYOMakerCategory($id) {
        $category = MYOMakerCategory::find($id);

        return view('admin.myomaker._delete_category', [
            'category' => $category,
        ]);
    }

    /**
     * Deletes an item category.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteMYOMakerCategory(Request $request, MYOMakerService $service, $id) {
        if ($id && $service->deleteMYOMakerCategory(MYOMakerCategory::find($id), Auth::user())) {
            flash('Category deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/myomaker');
    }

}