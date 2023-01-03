<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LevelController extends Controller {
    public function index(): JsonResponse {
        return response()->json(Level::all()); // Retourne un tableau de JSON de tous les niveaux
    }

    public function store(Request $request): JsonResponse { // Permet de créer un nouveau niveau (après vérification de la validité du token)
        $requiredFields = ['title', 'description', 'size', 'pattern']; // Liste des champs obligatoires de la requête
        $missingFields = array_diff($requiredFields, array_keys($request->all())); // tableau contenant la liste des champs obligatoires non renseignés danas la requête
        if ($missingFields) // S'il manque des champs obligatoires
            return response()->json("La requête doit contenir les champs " . implode(', ', $missingFields), Response::HTTP_BAD_REQUEST);
        $level = new Level(); // Initialisation du niveau avec les champs de la requête
        $level->title = $request->input('title');
        $level->description = $request->input('description');
        $level->size = $request->input('size');
        $level->pattern = $request->input('pattern');
        $login = TokenService::getLogin($request); // Recupère le login de l'utilosateur à l'aide du token
        $user = User::whereLogin($login)->first(); // Recupère l'utilisateur de l'utisateur pour récupérer son id
        $level->userId = $user->id; // Ajout de l'identifiant de l'utilisateur dans le niveau
        $level->finished = false; // Les niveaux ne sont pas encore finies (testées par le créateur) ni publié lors de leur création
        $level->published = false;
        $level->save(); // Enregistrement du niveau dans la base de donnée
        return response()->json($level);
    }

    public function show(int $id): JsonResponse {
        try {
            $level = Level::query()->findOrFail($id);
            return response()->json($level);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return  response()->json("Ce niveau n'existe pas", Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, int $id): JsonResponse {
        try {
            $level = Level::query()->findOrFail($id);
            $user = User::query()->findOrFail($level->id);
            $tokenLogin = TokenService::getLogin($request);
            if($tokenLogin !== $user->login)
                return response()->json("Vous ne pouvez pas modifier ce niveau", Response::HTTP_UNAUTHORIZED);
            $levelData = $request->only(['description', 'title', 'size', 'pattern', 'published', 'finished']);
            $level->fill($levelData)->save();
            return response()->json($level);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json("Ce niveau n'existe pas");
        }
    }

    public function destroy(Request $request, int $id): JsonResponse {
        try {
            $level = Level::query()->findOrFail($id);
            $user = User::query()->find($level->id);
            $tokenLogin = TokenService::getLogin($request);
            if($tokenLogin !== $user->login)
                return response()->json("Vous ne pouvez pas supprimer ce niveau", Response::HTTP_UNAUTHORIZED);
            $level->delete();
            return response()->json("Niveau supprimé");
        } catch (ModelNotFoundException $modelNotFoundException) {
            return response()->json("Ce niveau n'existe pas");
        }
    }
}
