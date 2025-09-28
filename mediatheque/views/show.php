<?php $base = dirname($_SERVER['PHP_SELF']); ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Détail du média</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Détail du média</h2>

    
    <div class="space-y-3">
      <p><span class="font-semibold text-gray-700">Titre :</span> <?= htmlspecialchars($media['title']) ?></p>
      <p><span class="font-semibold text-gray-700">Auteur :</span> <?= htmlspecialchars($media['author']) ?></p>
      <p><span class="font-semibold text-gray-700">Type :</span> <?= htmlspecialchars($media['type']) ?></p>
      <p>
        <span class="font-semibold text-gray-700">Disponibilité :</span>
        <?php if ($media['available']): ?>
          <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm">Disponible</span>
        <?php else: ?>
          <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm">Emprunté</span>
        <?php endif; ?>
      </p>
    </div>

    
    <?php if (!empty($media['illustration'])): ?>
      <div class="mt-6 text-center">
        <img src="<?= $base . $media['illustration'] ?>" class="max-h-60 mx-auto rounded shadow">
      </div>
    <?php endif; ?>

    
    <div class="mt-6 space-y-2">
      <?php if ($media['type'] === 'book'): ?>
        <p><span class="font-semibold text-gray-700">Nombre de pages :</span> <?= $media['page_number'] ?></p>
      <?php elseif ($media['type'] === 'movie'): ?>
        <p><span class="font-semibold text-gray-700">Durée :</span> <?= $media['duration'] ?> min</p>
        <p><span class="font-semibold text-gray-700">Genre :</span> <?= $media['genre'] ?></p>
      <?php elseif ($media['type'] === 'album'): ?>
        <p><span class="font-semibold text-gray-700">Nombre de titres :</span> <?= $media['track_number'] ?></p>
        <p><span class="font-semibold text-gray-700">Éditeur :</span> <?= $media['editor'] ?></p>
        <?php if (!empty($media['songs'])): ?>
          <h3 class="text-lg font-semibold text-gray-800 mt-4">Chansons</h3>
          <ul class="list-disc list-inside space-y-1 text-gray-700">
            <?php foreach ($media['songs'] as $song): ?>
              <li><?= htmlspecialchars($song['title']) ?> 
                (<?= $song['duration'] ?> min) - 
                <span class="text-yellow-600 font-medium">Note <?= $song['rating'] ?>/5</span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <!-- Bouton retour -->
    <div class="mt-8 text-center">
      <a href="?action=dashboard"
         class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
        ← Retour au tableau de bord
      </a>
    </div>
  </div>

</body>
</html>
