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
        'user_id', 'game_id', 'times_played', 'high_score', 'save_data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'game_scores';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;
}
