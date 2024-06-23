<?php

namespace App\Models\MYOMaker;

use App\Models\Model;

class MYOMakerCategory extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'myomaker_category';


    /**
     * Validation rules for image creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required',
    ];

    /**
     * Validation rules for image updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required',
    ];

}