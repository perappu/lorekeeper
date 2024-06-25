<?php

namespace App\Models\RandomGenerator;

use App\Models\Model;

class RandomCategory extends Model {
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
    protected $table = 'random_categories';
    /**
     * The primary key of the model.
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * Validation rules for character profile updating.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];
    
}