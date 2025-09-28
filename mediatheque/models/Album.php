<?php
namespace Models;

/**
 * Classe Album
 *
 * Représente un album musical dans la médiathèque.
 * Hérite de la classe abstraite Media.
 */
class Album extends Media
{
    public int $trackNumber;        /** @var int Nombre de pistes dans l'album */
    public string $editor;          /** @var string Nom de l'éditeur */

    public function __construct()
    {
        parent::__construct();
        $this->type = 'album';
    }

    public function save(): int
    {
        $mediaId = parent::save();
        $stmt = $this->db->prepare("SELECT id FROM albums WHERE media_id = :m");
        $stmt->execute([':m'=>$mediaId]);
        $exists = $stmt->fetch();
        if ($exists) {
            $stmt = $this->db->prepare("UPDATE albums SET track_number=:t, editor=:e WHERE media_id=:m");
            $stmt->execute([':t'=>$this->trackNumber, ':e'=>$this->editor, ':m'=>$mediaId]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO albums (media_id, track_number, editor) VALUES (:m, :t, :e)");
            $stmt->execute([':m'=>$mediaId, ':t'=>$this->trackNumber, ':e'=>$this->editor]);
        }
        return $mediaId;
    }

     /**
     * Récupère les détails d'un album avec ses chansons
     * @param int $id
     * @return array|null
     */
    public static function findWithSongs(int $id): ?array
    {
        $db = \Config\Database::getInstance();
        $stmt = $db->prepare("SELECT a.id AS album_id, m.*, a.track_number, a.editor FROM media m JOIN albums a ON a.media_id=m.id WHERE m.id=:id");
        $stmt->execute([':id'=>$id]);
        $album = $stmt->fetch();
        if (!$album) return null;
        $s = $db->prepare("SELECT * FROM songs WHERE album_id = :aid");
        $s->execute([':aid'=>$album['album_id']]);
        $album['songs'] = $s->fetchAll();
        return $album;
    }
}
