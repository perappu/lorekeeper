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
    public function getMYOMakerCreateCategory() {
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
    public function getMYOMakerEditCategory($id) {
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