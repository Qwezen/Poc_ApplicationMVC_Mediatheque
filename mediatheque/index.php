<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/config/Database.php';

spl_autoload_register(function($class){
    $class = ltrim($class, '\\');
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require $file;
});

use Controllers\UserController;
use Controllers\MediaController;


function setFlash(string $msg, string $type = 'success'): void {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function getFlash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}


function redirectTo(string $query = '') {
    $self = $_SERVER['PHP_SELF'];
    $url = $self . ($query ? '?' . $query : '');
    header('Location: ' . $url);
    exit;
}


$action = $_GET['action'] ?? 'login';

$uc = new UserController();
$mc = new MediaController();

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $uc->register($_POST);
    if (!empty($res['error'])) {
        $error = $res['error'];
        require __DIR__ . '/views/register.php';
    } else {
        setFlash("Inscription réussie !");
        redirectTo('action=dashboard');
    }
}
elseif ($action === 'register') {
    require __DIR__ . '/views/register.php';
}
elseif ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $uc->login($_POST);
    if (!empty($res['error'])) {
        $error = $res['error'];
        require __DIR__ . '/views/login.php';
    } else {
        setFlash("Connexion réussie !");
        redirectTo('action=dashboard');
    }
}
elseif ($action === 'login') {
    require __DIR__ . '/views/login.php';
}
elseif ($action === 'logout') {
    $uc->logout();
    setFlash("Déconnexion effectuée", "info");
    redirectTo('action=login');
}
elseif ($action === 'dashboard') {
    if (empty($_SESSION['user_id'])) {
        redirectTo('action=login');
    }
    $data = $mc->index($_GET);
    $medias = $data['medias'];
    $user = \Models\User::findById($_SESSION['user_id']);
    require __DIR__ . '/views/dashboard.php';
}
elseif ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) redirectTo('action=login');
    $mc->store($_POST, $_FILES);
    setFlash("Média ajouté avec succès !");
    redirectTo('action=dashboard');
}
elseif ($action === 'show' && isset($_GET['id'])) {
    $media = $mc->show((int)$_GET['id']);
    if (!$media) {
        echo "Média introuvable";
        exit;
    }
    require __DIR__ . '/views/show.php';
}
elseif ($action === 'borrow' && isset($_GET['id'])) {
    if (empty($_SESSION['user_id'])) redirectTo('action=login');
    $mc->borrow((int)$_GET['id']);
    setFlash("Média emprunté !");
    redirectTo('action=dashboard');
}
elseif ($action === 'return' && isset($_GET['id'])) {
    if (empty($_SESSION['user_id'])) redirectTo('action=login');
    $mc->giveBack((int)$_GET['id']);
    setFlash("Média rendu !");
    redirectTo('action=dashboard');
}
elseif ($action === 'delete' && isset($_GET['id'])) {
    if (empty($_SESSION['user_id'])) redirectTo('action=login');
    $mc->destroy((int)$_GET['id']);
    setFlash("Média supprimé !");
    redirectTo('action=dashboard');
}
elseif ($action === 'edit' && isset($_GET['id'])) {
    if (empty($_SESSION['user_id'])) redirectTo('action=login');
    $media = $mc->show((int)$_GET['id']);
    if (!$media) {
        echo "Média introuvable";
        exit;
    }
    require __DIR__ . '/views/edit.php';
}
elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    if (empty($_SESSION['user_id'])) redirectTo('action=login');
    $mc->update((int)$_GET['id'], $_POST, $_FILES);
    setFlash("Média modifié avec succès !");
    redirectTo('action=dashboard');
}
elseif ($action === 'songs' && isset($_GET['album_id'])) {
    if (empty($_SESSION['user_id'])) redirectTo('action=login');
    $album = \Models\Album::getAlbumWithSongs((int)$_GET['album_id']);
    require __DIR__ . '/views/songs.php';
}
elseif ($action === 'add_song' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['album_id'])) {
    if (empty($_SESSION['user_id'])) redirectTo('action=login');
    $song = new \Models\Song();
    $song->album_id = (int)$_GET['album_id'];
    $song->title = $_POST['title'];
    $song->duration = (float)$_POST['duration'];
    $song->rating = (int)$_POST['rating'];
    $song->save();
    setFlash("Chanson ajoutée !");
    redirectTo('action=songs&album_id=' . $_GET['album_id']);
}
else {
    header("HTTP/1.0 404 Not Found");
    echo "Page non trouvée";
}


