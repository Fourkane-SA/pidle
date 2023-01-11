<?php

namespace App\Http\Controllers;

use App\Models\game;
use App\Models\Level;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller {
    public function index() {
        return response()->json(game::all());
    }

    public function store(Request $request) {
        $levelId = $request->input('levelId');
        if(!$levelId)
            return response()->json("Le champ levelId doit être renseigné", Response::HTTP_BAD_REQUEST);
        $level = Level::find($levelId);
        if(!$level)
            return response()->json("Le niveau n'existe pas", Response::HTTP_NOT_FOUND);
        $userId = TokenService::getId($request);
        $game = new game();
        $game->userId = $userId;
        $game->levelId = $levelId;
        $game->completed = false;
        $game->save();
        return response()->json($game);
    }

    public function show(int $id) {
        $game = game::find($id);
        if(!$game)
            return response()->json("Ce niveau n'existe pas", Response::HTTP_NOT_FOUND);
        return response()->json($game);
    }

    public function showByUser(int $id) {
        $user = User::find($id);
        if(!$user)
            return response()->json("Cet utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
        $games = game::where('userId', $id)->get();
        return response()->json($games);
    }

    public function finish(int $id, Request $request) {
        $game = game::find($id);
        if(!$game)
            return response()->json("Ce niveau n'existe pas", Response::HTTP_NOT_FOUND);
        $userId = TokenService::getId($request);
        if($userId !== $game->userId)
            return response()->json("Vous n'etes pas autorisé à modifier ce niveau", Response::HTTP_UNAUTHORIZED);
        if(!$game->completed) {
            $game->completed = true;
            $game->save();
        }
        return response()->json($game);
    }
}
