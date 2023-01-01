<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifyToken;
use App\Models\User;
use App\Services\TokenService;
use Exception;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    /**
     * [GET] /api/users
     * @return JsonResponse
     * Retourne la liste des utilisateurs
     */
    public function index(): JsonResponse {
        return response()->json(User::all()); // Retourne la liste des utilisateurs
    }

    /**
     * [POST] /api/users
     * @param Request $request
     * Contient les différents champs de l'utilisateur à créer
     * @return JsonResponse
     * Retourne le nouvel utilisateur créé
     */
    public function store(Request $request): JsonResponse {
        $requiredFields = ['login', 'password', 'birth', 'email']; // Liste des champs obligatoires de la requête
        $missingFields = array_diff($requiredFields, array_keys($request->all())); // tableau contenant la liste des champs obligatoires non renseignés danas la requête
        if ($missingFields) // S'il manque des champs obligatoires
            return response()->json("La requête doit contenir les champs " . implode(', ', $missingFields), Response::HTTP_BAD_REQUEST);
        $user = new User(); // Création de l'utilisateur avec les champs renseignés dans la requête
        $user->login = $request->input('login');
        $user->birth = date('Y-m-d', strtotime($request->input('birth')));
        $user->idAvatar = $request->input('idAvatar');
        $user->password = password_hash($request->input('password'), PASSWORD_DEFAULT); // hachage du mot de passe
        $user->email = $request->input('email');
        $user->description = $request->input('description') ?: ""; // Champs facultatifs
        $user->firstname = $request->input('firstname') ?: "";
        $user->lastname = $request->input('lastname') ?: "";
        try {
            $user->saveOrFail(); // Enregistrement dans la base de donnée
            return response()->json($user, Response::HTTP_CREATED); // Retourne l'utilisateur créé sous forme JSON
        } catch (Throwable $e) { // L'enregistrement a échoué
            return response()->json("Le login ou l'adresse mail est déjà présent dans la base de données", Response::HTTP_UNAUTHORIZED);
        }
    }




}
