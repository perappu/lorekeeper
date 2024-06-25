<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\RandomGenerator\RandomObject;
use App\Models\RandomGenerator\RandomCategory;
use App\Services\RandomGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RandomGeneratorController extends Controller {
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
     * Shows the main index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {

        return view('admin.randomgenerator.randoms', [
            'categories' => RandomCategory::orderBy('id', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the index for a category.
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRandomCategoryIndex($id) {

        $category = RandomCategory::find($id);

        return view('admin.randomgenerator.random_category', [
            'category' => $category
        ]);
    }

    /**
     * Shows the create category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateRandomCategory() {
        return view('admin.randomgenerator.create_edit_random_category', [
            'category' => new RandomCategory,
        ]);
    }

        /**
     * Shows the edit item category page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditRandomCategory($id) {
        $category = RandomCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('admin.randomgenerator.create_edit_random_category', [
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
    public function postCreateEditRandomCategory(Request $request, RandomGeneratorService $service, $id = null) {
        $id ? $request->validate(RandomCategory::$rules) : $request->validate(RandomCategory::$rules);
        $data = $request->only([
            'name'
        ]);
        if ($id && $service->updateRandomCategory(RandomCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $category = $service->createRandomCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();

            return redirect()->to('admin/data/randomgenerator/category/edit/'.$category->id);
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
    public function getDeleteRandomCategory($id) {
        $category = RandomCategory::find($id);

        return view('admin.randomgenerator._delete_category', [
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
        if ($id && $service->deleteMYOMakerCategory(RandomCategory::find($id), Auth::user())) {
            flash('Category deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/randomgenerator/category');
    }

    /******** OBJECTS */






}