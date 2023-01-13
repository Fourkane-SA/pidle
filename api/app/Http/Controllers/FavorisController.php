<?php

namespace App\Http\Controllers;

use App\Models\Favoris;
use App\Models\Level;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 1. Compte les likes d'un niveau
 * 2. Ajouter / enlever un like d'un niveau
 * 3. Lister les likes d'un utilisateur
 */

class FavorisController extends Controller {

    public function countByLevel(int $idLevel) { // Retourne le nombre de likes d'un niveau
        $level = Level::find($idLevel);
        if(!$level)
            return response()->json("Ce niveau n'existe pas", Response::HTTP_NOT_FOUND);
        $favoris = Favoris::where('levelId', $idLevel)->where('isLiked', true)->get();
        return response()->json($favoris->count());
    }

    public function update(int $idLevel, Request $request) {
        $level = Level::find($idLevel);
        if(!$level)
            return response()->json("Ce niveau n'existe pas", Response::HTTP_NOT_FOUND);
        $idUser = TokenService::getId($request);
        $favoris = Favoris::where('levelId', $idLevel)->where('userId', $idUser)->first();
        if(!$favoris) {
            $favoris = new Favoris();
            $favoris->isLiked = true;
            $favoris->userId = $idUser;
            $favoris->levelId = $idLevel;
        } else {
            $favoris->isLiked = !$favoris->isLiked;
        }
        $favoris->save();
        return response()->json($favoris);
    }

    public function showByUser(int $idUser) {
        $user = User::find($idUser);
        if(!$user)
            return response()->json("Cet utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
        $favoris = Favoris::where('userId', $idUser)->where('isLiked', true)->get();
        return response()->json($favoris);
    }



}
