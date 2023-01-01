<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class TokenService { // Classe statique permettant de générer et valider un token
    public static function generateToken(User $user): string { // Permet de générer un token
        $publicKey = file_get_contents(base_path('keys/public.pem')); // Récupération de la clé publique

        $payload = [ // Contenu du token
            'sub' => $user->id, // Identifiant de l'utilisateur
            'name' => $user->login, // Nom d'utilisateur de l'utilisateur
            'iat' => time(), // Date de génération du token
            'exp' => time() + 3600 * 24 * 31, // Date d'expiration du token (1 mois)
        ];
        return JWT::encode($payload, $publicKey, 'HS256'); // Retourne le token généré
    }

    public static function validateToken(string $token) { // Permet de vérifier la validité d'un token
        $publicKey = file_get_contents(base_path('keys/public.pem')); // Recupère la clé publique
        $key = new Key($publicKey, 'HS256'); // Création d'un objet Key avec la clé publique et l'algorithme HS256
        try {
            return JWT::decode($token,$key, ['HS256']); // Décode le token et le retourne
        } catch (ExpiredException $e) {
            throw new ExpiredException('Le token JWT a expiré');
        } catch (Exception $e) {
            throw new Exception('Le token est invalide ');
        }
    }

    public static function getLogin(Request $request) { // Permet de récupérer le nom d'utilisateur d'un token
        $token = $request->header('Authorization'); // Recupère le token
        $decoded = self::validateToken($token); // Décode le token
        return $decoded->name; // Retourne le nom d'utilisateur stocké dans le token
    }
}
