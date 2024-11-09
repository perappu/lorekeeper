<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\User\User;
use Illuminate\Http\Request;

class InfoController extends Controller {
    public function getUser(Request $request) {
        $user = User::findOrFail($request->id);

        return $user;
    }

    public function getCharacter(Request $request) {
        $character = Character::findOrFail($request->id);

        return $character;
    }
}
