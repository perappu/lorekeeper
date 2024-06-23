<?php

namespace App\Models\MYOMaker;

use App\Models\Model;

class MYOMakerImage extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'category_id', 'image', 'name'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'myomaker_image';

    /**
     * Validation rules for image creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required',
        'category_id' => 'required',
        'image'      => 'required|mimes:jpeg,jpg,gif,png,webp|max:20000',
    ];

    /**
     * Validation rules for image updating.
     *
     * @var array
     */
    public static $updateRules = [
    ];

    /**
     * Get the category associated with the image.
     */
    public function category() {
        return $this->belongsTo(MYOMakerCategory::class, 'category_id');
    }

}