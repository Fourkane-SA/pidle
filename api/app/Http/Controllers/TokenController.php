<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenController extends Controller {

    public function generate (Request $request) { // Permet de générer un token JWT pour l'authentification
        $login = $request->input('login'); // Recupère le champ login de la requête
        $password = $request->input('password'); // Recupère le champ password de la requête
        if(!$login || !$password) // Si au moins un champ est manquant
            return response("La requête doit contenir les champs login et password", Response::HTTP_BAD_REQUEST);
        $user = User::whereLogin($login)->first(); // Recupère l'utilisateur dans la base de donnée
        if(!$user) // Si l'utilisateur n'existe pas
            return response("L'utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
        if(!password_verify($password, $user->password)) // Si le mot de passe renseigné ne correspond pas à celui stockée dans la base
            return response("Le mot de passe est incorrect", Response::HTTP_UNAUTHORIZED);
        $token = TokenService::generateToken($user); // Utilisation du service TokenService pour génerer un token
        return response()->json($token); // Retourne le token généré
    }

    public function decode (Request $request): JsonResponse { // Une fois le Middleware VerifyToken passé (token valide) permet de recupérer le propriétaire du token
        $login = TokenService::getId($request); // Récupère le token
        return response()->json($login); // Retourne le nom d'utilisateur
    }
}
