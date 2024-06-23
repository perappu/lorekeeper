<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Item\ItemCategory;
use App\Models\MYOMaker\MYOMakerCategory;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\MYOMaker\MYOMakerImage;

class MYOMakerController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / MYO Maker Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of myo maker categories and images.
    |
    */

    /**
     * Shows the category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {

        return view('admin.myomaker.index', [
            'images' => MYOMakerImage::orderBy('name', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCategoryIndex() {

        return view('admin.myomaker.categories', [
            'categories' => MYOMakerCategory::orderBy('name', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCategory() {
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
    public function getEditCategory($id) {
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
    public function postCreateEditCategory(Request $request, ItemService $service, $id = null) {
        $id ? $request->validate(MYOMakerCategory::$updateRules) : $request->validate(MYOMakerCategory::$createRules);
        $data = $request->only([
            'name'
        ]);
        if ($id && $service->updateCategory(MYOMakerCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $category = $service->createItemCategory($data, Auth::user())) {
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
    public function getDeleteCategory($id) {
        $category = ItemCategory::find($id);

        return view('admin.items._delete_item_category', [
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
    public function postDeleteCategory(Request $request, ItemService $service, $id) {
        if ($id && $service->deleteItemCategory(ItemCategory::find($id), Auth::user())) {
            flash('Category deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/myomaker');
    }

}