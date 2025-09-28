<?php

?>


<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <title>Modifier média</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Modifier le média</h2>

    <form method="post" action="?action=update&id=<?= $media['id'] ?>" enctype="multipart/form-data" class="space-y-4">

      
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
        <input id="title" name="title" value="<?= htmlspecialchars($media['title']) ?>" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
      </div>

     
      <div>
        <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Auteur</label>
        <input id="author" name="author" value="<?= htmlspecialchars($media['author']) ?>" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
      </div>

      
      <div>
        <label for="illustration" class="block text-sm font-medium text-gray-700 mb-1">Illustration</label>
        <input id="illustration" type="file" name="illustration"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
      </div>

     
      <?php if ($media['type'] === 'book'): ?>
        <div>
          <label for="page_number" class="block text-sm font-medium text-gray-700 mb-1">Pages</label>
          <input id="page_number" type="number" name="page_number" value="<?= $media['page_number'] ?? '' ?>"
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
      <?php elseif ($media['type'] === 'movie'): ?>
        <div>
          <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Durée (minutes)</label>
          <input id="duration" type="number" step="0.1" name="duration" value="<?= $media['duration'] ?? '' ?>"
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label for="genre" class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
          <input id="genre" name="genre" value="<?= $media['genre'] ?? '' ?>"
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
      <?php elseif ($media['type'] === 'album'): ?>
        <div>
          <label for="track_number" class="block text-sm font-medium text-gray-700 mb-1">Nombre de titres</label>
          <input id="track_number" type="number" name="track_number" value="<?= $media['track_number'] ?? '' ?>"
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label for="editor" class="block text-sm font-medium text-gray-700 mb-1">Éditeur</label>
          <input id="editor" name="editor" value="<?= $media['editor'] ?? '' ?>"
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
      <?php endif; ?>

    
      <div class="flex justify-between items-center pt-4">
        <a href="?action=dashboard" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow"> Retour</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Enregistrer</button>
      </div>

    </form>
  </div>

</body>
</html>
