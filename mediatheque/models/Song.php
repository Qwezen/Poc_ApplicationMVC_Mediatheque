<?php
namespace Models;

/**
 * Classe Song
 *
 * Représente une chanson appartenant à un album.
 */
class Song
{
    private \PDO $db;

    public int $id;         /** @var int ID de la chanson */
    public int $album_id;   /** @var int ID de l'album auquel elle appartient */
    public string $title;   /** @var string Titre de la chanson */
    public float $duration; /** @var float Durée de la chanson en minutes */
    public int $rating;     /** @var int Note de la chanson (0 à 5) */

    public function __construct()
    {
        $this->db = \Config\Database::getInstance();
    }

     /**
     * Enregistre la chanson en base
     * @return int ID de la chanson
     */
    public function save(): int
    {
        if (isset($this->id)) {
            $stmt = $this->db->prepare("UPDATE songs SET title=:t, duration=:d, rating=:r WHERE id=:id");
            $stmt->execute([':t'=>$this->title, ':d'=>$this->duration, ':r'=>$this->rating, ':id'=>$this->id]);
            return $this->id;
        } else {
            $stmt = $this->db->prepare("INSERT INTO songs (album_id, title, duration, rating) VALUES (:a, :t, :d, :r)");
            $stmt->execute([':a'=>$this->album_id, ':t'=>$this->title, ':d'=>$this->duration, ':r'=>$this->rating]);
            return (int)$this->db->lastInsertId();
        }
    }

    /**
     * Supprime la chanson en base enfonction de l'ID
     */
    public static function delete(int $id): bool
    {
        $db = \Config\Database::getInstance();
        $stmt = $db->prepare("DELETE FROM songs WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }
}
