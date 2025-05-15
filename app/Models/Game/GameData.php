<?php

namespace App\Models\Game;

use App\Models\Game\Game;
use App\Models\Model;

class GameData extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id', 'game', 'data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'game_data';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the item that this tag is attached to.
     */
    public function game() {
        return $this->belongsTo(Game::class);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the tag name formatted according to its colours as defined in the config file.
     *
     * @return string
     */
    public function getDisplayTagAttribute() {
        $tag = config('lorekeeper.item_tags.'.$this->game);
        if ($tag) {
            return '<span class="badge" style="color: '.$tag['text_color'].';background-color: '.$tag['background_color'].';">'.$tag['name'].'</span>';
        }

        return null;
    }

    /**
     * Get the tag's display name.
     *
     * @return mixed
     */
    public function getName() {
        return config('lorekeeper.game_options.'.$this->game.'.name');
    }

    /**
     * Gets the URL of the tag's editing page.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/games/edit/'.$this->game_id);
    }

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute() {
        return json_decode($this->attributes['data'], true);
    }

    /**
     * Get the service associated with this tag.
     *
     * @return mixed
     */
    public function getServiceAttribute() {
        $class = 'App\Services\Game\\'.str_replace(' ', '', ucwords(str_replace('_', ' ', $this->game))).'Service';

        return new $class();
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Get the data used for editing the tag.
     *
     * @return mixed
     */
    public function getEditData() {
        return $this->service->getEditData();
    }

    /**
     * Get the data associated with the tag.
     *
     * @return mixed
     */
    public function getData() {
        return $this->service->getGameData($this);
    }
}
