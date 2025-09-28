<?php

$user = $user ?? null;
$medias = $medias ?? [];
$base = dirname($_SERVER['PHP_SELF']);

?>

<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>Mediatheque</title>
  
</head>

<body class="bg-gray-100 text-gray-800 font-semibold">

<nav class="bg-blue-600 text-white shadow-md">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <h1 class="text-xl font-bold">Mediatheque</h1>
    <div>
      <?php if ($user): ?>
        <span class="mr-4">Bienvenue <strong><?=htmlspecialchars($user['username'])?></strong></span>
        <a href="?action=logout" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Se déconnecter</a>
      <?php else: ?>
        <a href="?action=login" class="px-3 py-1 hover:underline">Connexion</a>
        <a href="?action=register" class="ml-2 px-3 py-1 hover:underline">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="max-w-7xl mx-auto p-6">

  
  <?php if ($flash = getFlash()): ?>
    <div class="mb-4 p-3 rounded 
      <?= $flash['type']==='success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' ?>">
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
  <?php endif; ?>


  <h2 class="text-2xl font-semibold mb-4">Liste des médias</h2>

  <form method="get" action="" class="flex flex-wrap gap-2 mb-6 bg-white p-4 rounded shadow">
    <input type="hidden" name="action" value="dashboard">
    <input name="search" placeholder="Recherche par titre ou auteur" value="<?=htmlspecialchars($_GET['search'] ?? '')?>" class="flex-1 border rounded px-3 py-2">

    <select name="sort" class="border rounded px-2 py-2">
      <option value="title">Titre</option>
      <option value="author">Auteur</option>
      <option value="type">Type</option>
    </select>

    <select name="dir" class="border rounded px-2 py-2">
      <option value="ASC">Ascendant</option>
      <option value="DESC">Descendant</option>
    </select>

    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Filtrer</button>

  </form>

  
<div class="overflow-x-auto">
  <table class="min-w-full bg-white border border-gray-200 shadow-lg rounded-lg">
    <thead class="bg-gray-200">
      <tr>
        <th class="px-6 py-3 text-left text-gray-700 font-semibold">ID</th>
        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Illustration</th>
        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Titre</th>
        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Auteur</th>
        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Type</th>
        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Disponibilité</th>
        <th class="px-6 py-3 text-left text-gray-700 font-semibold">Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($medias)): ?>
        <?php foreach ($medias as $m): ?>
          <tr class="border-gray hover:bg-gray-50">
            <td class="px-6 py-3"><?= $m['id'] ?></td>
            <td class="px-6 py-3">
              <?php if (!empty($m['illustration'])): ?>
                <img src="<?= $base . $m['illustration'] ?>" class="h-16 object-cover rounded">
              <?php endif; ?>
            </td>
            <td class="px-6 py-3"><?= htmlspecialchars($m['title']) ?></td>
            <td class="px-6 py-3"><?= htmlspecialchars($m['author']) ?></td>
            <td class="px-6 py-3"><?= htmlspecialchars($m['type']) ?></td>
            <td class="px-6 py-3">
              <?php if ($m['available']): ?>
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm">Disponible</span>
              <?php else: ?>
                <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm">Emprunté</span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-3 space-x-2">
              <a href="?action=show&id=<?= $m['id'] ?>" 
                 class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm">Voir</a>
              <?php if (!empty($_SESSION['user_id'])): ?>
                <?php if ($m['available']): ?>
                  <a href="?action=borrow&id=<?= $m['id'] ?>" 
                     class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded-lg text-sm">Emprunter</a>
                <?php else: ?>
                  <a href="?action=return&id=<?= $m['id'] ?>" 
                     class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm">Rendre</a>
                <?php endif; ?>
                <a href="?action=edit&id=<?= $m['id'] ?>" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg text-sm">Modifier</a>
                <a href="?action=delete&id=<?= $m['id'] ?>" 
                   onclick="return confirm('Supprimer ?')" 
                   class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm">Supprimer</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>

      <?php else: ?>
        <tr>
          <td colspan="7" class="px-6 py-6 text-center text-gray-600">Aucun média enregistré.</td>
        </tr>
      <?php endif; ?>

    </tbody>
  </table>
</div>


  
  <?php if (!empty($_SESSION['user_id'])): ?>
    <h2 class="text-2xl font-semibold mt-8 mb-4">Ajouter un média</h2>
    <form method="post" action="?action=store" enctype="multipart/form-data"
          class="bg-white p-6 rounded shadow space-y-4">
      <div>
        <label class="block font-medium">Type</label>
        <select name="type" class="border rounded px-3 py-2 w-full">
          <option value="book">Livre</option>
          <option value="movie">Film</option>
          <option value="album">Album</option>
        </select>
      </div>
      <div>
        <label class="block font-medium">Titre</label>
        <input name="title" required class="border rounded px-3 py-2 w-full">
      </div>
      <div>
        <label class="block font-medium">Auteur</label>
        <input name="author" required class="border rounded px-3 py-2 w-full">
      </div>
      <div>
        <label class="block font-medium">Illustration</label>
        <input type="file" name="illustration" class="border rounded px-3 py-2 w-full">
      </div>

      <div id="bookFields">
        <label class="block font-medium">Pages</label>
        <input name="page_number" type="number" class="border rounded px-3 py-2 w-full">
      </div>

      <div id="movieFields" style="display:none;">
        <label class="block font-medium">Durée</label>
        <input name="duration" type="number" step="0.1" class="border rounded px-3 py-2 w-full">
        <label class="block font-medium mt-2">Genre</label>
        <input name="genre" class="border rounded px-3 py-2 w-full">
      </div>

      <div id="albumFields" style="display:none;">
        <label class="block font-medium">Nombre de titres</label>
        <input name="track_number" type="number" class="border rounded px-3 py-2 w-full">
        <label class="block font-medium mt-2">Éditeur</label>
        <input name="editor" class="border rounded px-3 py-2 w-full">
      </div>

      <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Ajouter</button>
    </form>

    <script>
      const typeEl = document.querySelector('select[name="type"]');
      const bookFields = document.getElementById('bookFields');
      const movieFields = document.getElementById('movieFields');
      const albumFields = document.getElementById('albumFields');

      typeEl.addEventListener('change', function(){
        bookFields.style.display = this.value==='book' ? '' : 'none';
        movieFields.style.display = this.value==='movie' ? '' : 'none';
        albumFields.style.display = this.value==='album' ? '' : 'none';
      });
    </script>
  <?php endif; ?>

</div>
</body>
</html>
