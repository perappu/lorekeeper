<?php

namespace App\Models\RandomGenerator;

use App\Models\Model;
use App\Models\RandomGenerator\RandomCategory;

class RandomObject extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'link', 'category_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'random_objects';
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
        'text' => 'required',
        'category_id' => 'required',
    ];

    /**
     * Get the category this object belongs to.
     */
    public function category() {
        return $this->belongsTo(RandomCategory::class, 'character_id');
    }

}