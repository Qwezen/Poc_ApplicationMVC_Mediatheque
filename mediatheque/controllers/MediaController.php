<?php
namespace Controllers;

use Models\Media;
use Models\Book;
use Models\Movie;
use Models\Album;
use Models\Song;
use Config\Database;

/**
 * Contrôleur MediaController
 *
 * Gère les actions liées aux médias : affichage, ajout, modification,
 * emprunt, retour, suppression.
 */
class MediaController
{
    /**
     * Affiche la liste des médias
     * @param array $query Paramètres de tri et recherche
     * @return array Données à afficher
     */
    public function index(array $query): array
    {
        $sort = $query['sort'] ?? 'title';
        $dir = $query['dir'] ?? 'ASC';
        $search = $query['search'] ?? null;
        $medias = Media::all($sort, $dir, $search);
        return ['medias'=>$medias];
    }

    public function show(int $id): ?array
    {
        $m = Media::find($id);
        if (!$m) return null;
        if ($m['type'] === 'book') return Book::findWithDetails($id);
        if ($m['type'] === 'movie') return Movie::findWithDetails($id);
        if ($m['type'] === 'album') return Album::findWithSongs($id);
        return $m;
    }

    /**
     * Gère l'upload d'une image
     * @param array $file Fichier envoyé
     * @return string|null Chemin de l'image ou null
     */
    private function handleUpload(array $file): ?string
    {
        if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
        $allowed = ['image/jpeg','image/png','image/gif'];
        if (!in_array($file['type'], $allowed)) return null;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $destDir = __DIR__ . '/../assets/uploads';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        $basename = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $destDir . '/' . $basename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;
        return '/assets/uploads/' . $basename;
    }

    public function store(array $data, array $files): array
    {
        if (empty($_SESSION['user_id'])) return ['error'=>'Authentification requise.'];

        $type = $data['type'] ?? 'book';
        $title = trim($data['title'] ?? '');
        $author = trim($data['author'] ?? '');
        $illustration = $this->handleUpload($files['illustration'] ?? []);
        $available = 1;

        if (!$title || !$author) return ['error'=>'Titre et auteur requis.'];

        if ($type === 'book') {
            $book = new Book();
            $book->title = $title;
            $book->author = $author;
            $book->available = $available;
            $book->illustration = $illustration;
            $book->pageNumber = (int)($data['page_number'] ?? 0);
            $book->save();
        } elseif ($type === 'movie') {
            $movie = new Movie();
            $movie->title = $title;
            $movie->author = $author;
            $movie->available = $available;
            $movie->illustration = $illustration;
            $movie->duration = (float)($data['duration'] ?? 0);
            $movie->genre = $data['genre'] ?? 'Unknown';
            $movie->save();
        } elseif ($type === 'album') {
            $album = new Album();
            $album->title = $title;
            $album->author = $author;
            $album->available = $available;
            $album->illustration = $illustration;
            $album->trackNumber = (int)($data['track_number'] ?? 0);
            $album->editor = $data['editor'] ?? '';
            $albumId = $album->save();

            if (!empty($data['songs']) && is_array($data['songs'])) {
                foreach ($data['songs'] as $s) {
                    $song = new Song();
                    $song->album_id = $albumId;
                    $song->title = $s['title'];
                    $song->duration = (float)$s['duration'];
                    $song->rating = max(0, min(5, (int)$s['rating']));
                    $song->save();
                }
            }
        }

        return ['success'=>true];
    }

    public function update(int $id, array $data, array $files): array
    {
        if (empty($_SESSION['user_id'])) return ['error'=>'Authentification requise.'];

        $media = Media::find($id);
        if (!$media) return ['error'=>'Média introuvable.'];

        $illustration = $this->handleUpload($files['illustration'] ?? []);

        if ($media['type'] === 'book') {
            $book = new Book();
            $book->id = $id;
            $book->title = $data['title'] ?? $media['title'];
            $book->author = $data['author'] ?? $media['author'];
            $book->available = isset($data['available']) ? 1 : $media['available'];
            $book->illustration = $illustration ?? $media['illustration'];
            $book->pageNumber = (int)($data['page_number'] ?? 0);
            $book->save();
        }

        if ($media['type'] === 'movie') {
            $movie = new Movie();
            $movie->id = $id;
            $movie->title = $data['title'] ?? $media['title'];
            $movie->author = $data['author'] ?? $media['author'];
            $movie->available = isset($data['available']) ? 1 : $media['available'];
            $movie->illustration = $illustration ?? $media['illustration'];
            $movie->duration = (float)($data['duration'] ?? 0);
            $movie->genre = $data['genre'] ?? 'Unknown';
            $movie->save();
        }

        if ($media['type'] === 'album') {
            $album = new Album();
            $album->id = $id;
            $album->title = $data['title'] ?? $media['title'];
            $album->author = $data['author'] ?? $media['author'];
            $album->available = isset($data['available']) ? 1 : $media['available'];
            $album->illustration = $illustration ?? $media['illustration'];
            $album->trackNumber = (int)($data['track_number'] ?? 0);
            $album->editor = $data['editor'] ?? '';
            $album->save();
        }

        return ['success'=>true];
    }

    public function destroy(int $id): array
    {
        if (empty($_SESSION['user_id'])) return ['error'=>'Authentification requise.'];
        Media::delete($id);
        return ['success'=>true];
    }

     /**
     * Emprunte un média
     * @param int $id Identifiant
     * @return array Résultat
     */
    public function borrow(int $id): array
    {
        if (empty($_SESSION['user_id'])) return ['error'=>'Authentification requise.'];

        $m = Media::find($id);
        if (!$m) return ['error'=>'Média introuvable.'];
        if (!$m['available']) return ['error'=>'Média déjà emprunté.'];

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE media SET available = 0 WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return ['success'=>true];
    }

    /**
     * Rend disponible un média
     * @param int $id Identifiant
     * @return array Résultat
     */
    public function giveBack(int $id): array
    {
        if (empty($_SESSION['user_id'])) return ['error'=>'Authentification requise.'];

        $m = Media::find($id);
        if (!$m) return ['error'=>'Média introuvable.'];
        if ($m['available']) return ['error'=>'Média déjà disponible.'];

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE media SET available = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return ['success'=>true];
    }
}

