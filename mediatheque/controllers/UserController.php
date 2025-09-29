<?php

namespace Controllers;

use Models\User;

/**
 * Contrôleur UserController
 *
 * Gère l'inscription, la connexion et la déconnexion des utilisateurs.
 */
class UserController
{
    /**
     * Inscrit un nouvel utilisateur
     * @param array $data Données du formulaire
     * @return array Résultat (succès ou erreur)
     */
    public function register(array $data): array
    {
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirm = $data['password_confirm'] ?? '';

        if (!$username || !$email || !$password) return ['error'=>'Tous les champs sont requis.'];

        if ($password !== $confirm) return ['error'=>'Les mots de passe ne correspondent pas.'];

        /* Regex pour verifier le niveau sécurité du mot de passe */
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';

        if (!preg_match($pattern, $password)) {
            return ['error'=>'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.'];
        }
        if (stripos($password, $username) !== false) {
            return ['error'=>'Le mot de passe ne doit pas contenir l’identifiant.'];
        }

        if (User::findByUsername($username)) return ['error'=>'Nom d\'utilisateur déjà pris.'];

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $userId = $user->create();

        $_SESSION['user_id'] = $userId;
        return ['success'=>true];
    }

     /**
     * Connecte un utilisateur existant
     * @param array $data Données du formulaire
     * @return array Résultat (succès ou erreur)
     */
    public function login(array $data): array
    {
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (!$username || !$password) return ['error' => 'Tous les champs sont requis.'];

        $found = User::findByUsername($username);
        if (!$found) return ['error' => 'Identifiants invalides.'];

        if (!password_verify($password, $found['password'])) return ['error' => 'Identifiants invalides.'];

        session_regenerate_id(true);
        $_SESSION['user_id'] = $found['id'];
        return ['success'=>true];
    }

     /**
     * Déconnecte l'utilisateur actuel
     * @return void
     */
    public function logout(): void
    {
       
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            setcookie(session_name(), '', time() - 42000);
        }
        session_destroy();
       
    }
}
