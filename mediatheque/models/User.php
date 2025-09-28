<?php
namespace Models;

use Config\Database;
use PDO;

/**
 * Classe User
 *
 * Représente un utilisateur de la médiathèque.
 * Permet l'inscription, la connexion et la récupération d'informations utilisateur.
 */
class User
{
    private PDO $db;                /** @var PDO Instance de connexion à la base de données */

    public int $id;                 /** @var int Identifiant unique de l'utilisateur */
    public string $username;        /** @var string Nom d'utilisateur (identifiant de connexion) */
    public string $email;           /** @var string Email de l'utilisateur */
    public string $password;        /** @var string Mot de passe hashé */

     /**
     * Constructeur : initialise la connexion à la base de données
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

     /**
     * Enregistre un nouvel utilisateur dans la base de données
     *
     * @return int ID de l'utilisateur nouvellement créé
     */
    public function create(): int
    {
        $stmt = $this->db->prepare("INSERT INTO users (username,email,password) VALUES (:u,:e,:p)");
        $stmt->execute([':u'=>$this->username, ':e'=>$this->email, ':p'=>$this->password]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Récupère un utilisateur par son nom d'utilisateur, sert a vérifier si le nom ne se trouve pas dans le mot de passe.
     *
     * @param string $username Nom de l'utilisateur
     * @return array|null Données utilisateur ou null si introuvable
     */
    public static function findByUsername(string $username): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :u");
        $stmt->execute([':u'=>$username]);
        return $stmt->fetch() ?: null;
    }

     /**
     * Récupère un utilisateur par son ID
     *
     * @param int $id Identifiant de l'utilisateur
     * @return array|null Données utilisateur ou null si introuvable
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, email, created_at, updated_at FROM users WHERE id = :id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch() ?: null;
    }
}
