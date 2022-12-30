<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    /**
     * /api/users
     * @return JsonResponse
     * Retourne la liste des utilisateurs
     */
    public function index(): JsonResponse {
        return response()->json(User::all());
    }

    public function store(Request $request): JsonResponse {
        $login = $request->input('login');
        $description = $request->input('description');
        $birth = $request->input('birth');
        $idAvatar = $request->input('idAvatar');
        $password = $request->input('password');
        $email = $request->input('email');
        $firstname = $request->input('firstname');
        $lastname = $request->input('lastname');
        if(!$login || !$password || !$birth || !$email) {
            return response()->json("La requete doit contenir les champs login, birth, password et email", Response::HTTP_BAD_REQUEST);
        }
        $user = new User();
        $user->login = $login;
        $user->description = $description;
        $user->birth = date('Y-m-d',strtotime($birth));
        $user->idAvatar = $idAvatar;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->email = $email;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        try {
            $user->saveOrFail();
            return response()->json($user, Response::HTTP_CREATED);
        } catch (Throwable $e) {
            return response()->json("Le login ou l'adresse mail est déjà présent dans la base de donnée", Response::HTTP_UNAUTHORIZED);
        }
    }
}
