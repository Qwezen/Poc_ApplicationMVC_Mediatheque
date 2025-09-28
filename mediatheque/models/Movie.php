<?php
namespace Models;

/**
 * Classe Movie
 *
 * Représente un film dans la médiathèque.
 * Hérite de la classe abstraite Media.
 */
class Movie extends Media
{
    public float $duration;     /** @var float Durée du film en minutes */
    public string $genre;       /** @var string Genre du film */

      /**
     * Constructeur : initialise le type et la base
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = 'movie';
    }

    public function save(): int
    {
        $mediaId = parent::save();
        $stmt = $this->db->prepare("SELECT id FROM movies WHERE media_id = :m");
        $stmt->execute([':m'=>$mediaId]);
        $exists = $stmt->fetch();
        if ($exists) {
            $stmt = $this->db->prepare("UPDATE movies SET duration=:d, genre=:g WHERE media_id=:m");
            $stmt->execute([':d'=>$this->duration, ':g'=>$this->genre, ':m'=>$mediaId]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO movies (media_id, duration, genre) VALUES (:m, :d, :g)");
            $stmt->execute([':m'=>$mediaId, ':d'=>$this->duration, ':g'=>$this->genre]);
        }
        return $mediaId;
    }

     /**
     * Récupère les détails d'un film par son ID
     * @param int $id
     * @return array|null
     */
    public static function findWithDetails(int $id): ?array
    {
        $db = \Config\Database::getInstance();
        $stmt = $db->prepare("SELECT m.*, mv.duration, mv.genre FROM media m JOIN movies mv ON mv.media_id=m.id WHERE m.id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch() ?: null;
    }
}
