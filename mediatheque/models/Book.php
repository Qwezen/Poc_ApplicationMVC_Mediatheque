<?php
namespace Models;

/**
 * Classe Book
 *
 * Représente un livre dans la médiathèque.
 * Hérite de la classe abstraite Media.
 */
class Book extends Media
{
    public int $pageNumber;  /** @var int Nombre de pages du livre */

    /**
    * Constructeur : initialise le type et la base
    */
    public function __construct()
    {
        parent::__construct();
        $this->type = 'book';
    }

    public function save(): int
    {
        $mediaId = parent::save();
        $stmt = $this->db->prepare("SELECT id FROM books WHERE media_id = :m");
        $stmt->execute([':m'=>$mediaId]);
        $exists = $stmt->fetch();
        if ($exists) {
            $stmt = $this->db->prepare("UPDATE books SET page_number = :p WHERE media_id = :m");
            $stmt->execute([':p'=>$this->pageNumber, ':m'=>$mediaId]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO books (media_id, page_number) VALUES (:m, :p)");
            $stmt->execute([':m'=>$mediaId, ':p'=>$this->pageNumber]);
        }
        return $mediaId;
    }

    /**
     * Récupère les détails d'un livre par son ID
     * @param int $id
     * @return array|null
    */
    public static function findWithDetails(int $id): ?array
    {
        $db = \Config\Database::getInstance();
        $stmt = $db->prepare("SELECT m.*, b.page_number FROM media m JOIN books b ON b.media_id=m.id WHERE m.id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch() ?: null;
    }
}
