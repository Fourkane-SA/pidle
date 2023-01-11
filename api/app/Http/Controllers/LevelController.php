<?php

namespace App\Http\Controllers;

use App\Models\Level;
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
        $id = TokenService::getId($request); // Recupère le login de l'utilosateur à l'aide du token
        $level->userId = $id; // Ajout de l'identifiant de l'utilisateur dans le niveau
        $level->finished = false; // Les niveaux ne sont pas encore finies (testées par le créateur) ni publié lors de leur création
        $level->published = false;
        $level->save(); // Enregistrement du niveau dans la base de donnée
        return response()->json($level);
    }

    public function show(int $id): JsonResponse { // Permet de recupérer un niveau par son id
        try {
            $level = Level::query()->findOrFail($id); // Lève une exception si le niveau n'existe pas
            return response()->json($level);
        } catch (ModelNotFoundException $modelNotFoundException) { // Si le niveau n'existe aps
            return  response()->json("Ce niveau n'existe pas", Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, int $id): JsonResponse { // Mis à jour d'un niveau (après vérification du token)
        try {
            $level = Level::query()->findOrFail($id); // Lève une exception si le niveau n'existe pas
            $tokenId = TokenService::getId($request); // Recupère l'id de l'utilisateur stocké dans le token
            if($level->userId !== $tokenId) // Si le créateur du niveau n'est pas celui de l'utilisateur connecté
                return response()->json("Vous ne pouvez pas modifier ce niveau", Response::HTTP_UNAUTHORIZED);
            $levelData = $request->only(['description', 'title', 'size', 'pattern', 'published', 'finished']); // Champs pouvant etre modifié
            $level->fill($levelData)->save(); // Modification des champs non nuls
            return response()->json($level);
        } catch (ModelNotFoundException $modelNotFoundException) { // Si le niveau n'a pas été trouvé
            return response()->json("Ce niveau n'existe pas");
        }
    }

    public function destroy(Request $request, int $id): JsonResponse { // Supprime un niveau (après vérification du token)
        try {
            $level = Level::query()->findOrFail($id); // Lève une exception si le niveau n'existe pas
            $tokenId = TokenService::getId($request); // Recupère l'id de l'utilisateur stocké dans le token
            if($level->userId !== $tokenId) // Si le créateur du niveau n'est pas celui de l'utilisateur connecté
                return response()->json("Vous ne pouvez pas supprimer ce niveau", Response::HTTP_UNAUTHORIZED);
            $level->delete(); // Supprime le niveau
            return response()->json("Niveau supprimé");
        } catch (ModelNotFoundException $modelNotFoundException) { // Le niveau n'a pas été trouvé
            return response()->json("Ce niveau n'existe pas");
        }
    }
}
