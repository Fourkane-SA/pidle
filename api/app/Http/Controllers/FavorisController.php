<?php

namespace App\Http\Controllers;

use App\Models\Favoris;
use App\Models\Level;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class FavorisController extends Controller {

    public function countByLevel(int $idLevel) { // Retourne le nombre de likes d'un niveau
        $level = Level::find($idLevel);
        if(!$level)
            return response()->json("Ce niveau n'existe pas", Response::HTTP_NOT_FOUND);
        $favoris = Favoris::where('levelId', $idLevel)->where('isLiked', true)->get();
        return response()->json($favoris->count());
    }

    public function update(int $idLevel, Request $request) { // Met à jour le favoris du niveau après vérification du token
        $level = Level::find($idLevel); // Recupère le niveau via son ID
        if(!$level) // Si le niveau n'existe pas
            return response()->json("Ce niveau n'existe pas", Response::HTTP_NOT_FOUND);
        $idUser = TokenService::getId($request); // Recupère l'identifiant de l'utisateur ayant effectué la requête
        $favoris = Favoris::where('levelId', $idLevel)->where('userId', $idUser)->first(); // Vérifie si le champ favoris est déjà présent dans la base de donnée
        if(!$favoris) { // Si l'utilisateur n'a jamais mis ce niveau dans ses favoris
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

    public function showByUser(int $idUser) { // Retourne tous les niveaux que l'utilisateur a ajouté dans ses favoris
        $user = User::find($idUser);
        if(!$user)
            return response()->json("Cet utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
        $favoris = Favoris::where('userId', $idUser)->where('isLiked', true)->get();
        return response()->json($favoris);
    }



}
