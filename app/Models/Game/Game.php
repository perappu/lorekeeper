<?php

namespace App\Models\Game;

use App\Models\Model;

class Game extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'description', 'parsed_description', 'is_active', 'hash',
        'currency_id', 'currency_cap', 'score_ratio',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games';
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'        => 'required|unique:item_categories|between:3,100',
        'description' => 'nullable',
        'image'       => 'mimes:png',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'        => 'required|between:3,100',
        'description' => 'nullable',
        'image'       => 'mimes:png',
    ];

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the shop's name, linked to its purchase page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return '<a href="'.$this->url.'" class="display-game">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/games';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getGameImageFileNameAttribute() {
        return $this->hash.$this->id.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getGameImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getGameImageUrlAttribute() {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->gameImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('games/'.$this->id);
    }

    /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/games/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     *
     * @return string
     */
    public function getAdminPowerAttribute() {
        return 'edit_data';
    }

    /**
     * Gets the file directory for the game
     *
     * @return string
     */
    public function getFileDirectoryAttribute() {
        return '/gamesfiles/'.$this->id;
    }

    /**
     * Gets the directory where arbitrary game files are stored
     *
     * @return string
     */
    public function getFilesDirectoryAttribute() {
        return '/gamesfiles/'.$this->id.'/files';
    }

    /**
     * Gets  directory where arbitrary game files are stored, but as an absolute url
     *
     * @return string
     */
    public function getFileDirectoryUrlAttribute() {
        return url('/gamesfiles/'.$this->id);
    }

    /**
     * Gets the absolute URL of the game's main HTML file
     *
     * @return string
     */
    public function getHTMLUrlAttribute() {
        return url('gamesfiles/'.$this->id.'/'.$this->id.'.html');
    }
}
