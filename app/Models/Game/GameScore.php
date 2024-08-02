<?php

namespace App\Models\Game;

use App\Models\Model;

class GameScore extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'game_id', 'times_played',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'game_scores';

    /*
     * Validation rules for creation.
     *
     * @var array
     */

}
