<?php
namespace Models;

use Config\Database;
use PDO;

/**
 * Classe abstraite Media
 *
 * Représente un média générique dans la médiathèque.
 * Cette classe ne peut pas être instanciée directement.
 * Elle sert de base pour les types spécifiques : Book, Movie, Album.
 */

abstract class Media
{
    public int $id;                 /** @var int Identifiant unique du média */
    public string $title;           /** @var string Titre du média */
    public string $author;          /** @var string Auteur ou créateur du média */
    public bool $available;         /** @var bool Indique si le média est disponible à l'emprunt */
    public ?string $illustration;   /** @var string|null Chemin vers l'image d'illustration */
                                    /** @var string Type du média (book, movie, album) */
    public string $type;            

    protected PDO $db;

    /**
    * Constructeur : initialise la connexion à la base de données
    */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Marque le média comme emprunté
     * @return bool true si l'opération réussit
     */
    public function borrow(): bool
    {
        if (!$this->available) return false;
        $stmt = $this->db->prepare("UPDATE media SET available = 0 WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    /**
     * Marque le média comme rendu
     * @return bool true si l'opération réussit
     */
    public function return(): bool
    {
        if ($this->available) return false;
        $stmt = $this->db->prepare("UPDATE media SET available = 1 WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    /**
     * Récupère tous les médias avec tri et recherche
     * @param string $sort Champ de tri
     * @param string $direction Direction du tri (ASC ou DESC)
     * @param string $search Terme de recherche
     * @return array Liste des médias
     */
    public static function all(string $sort = 'title', string $direction = 'ASC', ?string $search = null): array
    {
        $db = Database::getInstance();
        $allowedSort = ['title','author','available','type'];
        if (!in_array($sort, $allowedSort)) $sort = 'title';
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        if ($search) {
            $stmt = $db->prepare("SELECT * FROM media WHERE title LIKE :s OR author LIKE :s ORDER BY $sort $direction");
            $stmt->execute([':s' => "%$search%"]);
        } else {
            $stmt = $db->query("SELECT * FROM media ORDER BY $sort $direction");
        }
        return $stmt->fetchAll();
    }

     /**
     * Trouve un média par son ID
     * @param int $id Identifiant du média
     * @return array|null Données du média ou null si introuvable
     */
    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM media WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Enregistre ou met à jour le média en base
     * @return int ID du média
     */
    public function save(): int
    {
        if (isset($this->id)) {
            $stmt = $this->db->prepare("UPDATE media SET title=:title, author=:author, available=:available, illustration=:illustration, type=:type WHERE id=:id");
            $stmt->execute([
                ':title'=>$this->title, ':author'=>$this->author, ':available'=>$this->available?1:0,
                ':illustration'=>$this->illustration, ':type'=>$this->type, ':id'=>$this->id
            ]);
            return $this->id;
        } else {
            $stmt = $this->db->prepare("INSERT INTO media (title,author,available,illustration,type) VALUES (:title,:author,:available,:illustration,:type)");
            $stmt->execute([
                ':title'=>$this->title, ':author'=>$this->author,
                ':available'=>$this->available?1:0, ':illustration'=>$this->illustration, ':type'=>$this->type
            ]);
            return (int)$this->db->lastInsertId();
        }
    }

    /**
     * Supprime un média par son ID
     * @param int $id Identifiant du média
     * @return bool true si supprimé
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM media WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}





