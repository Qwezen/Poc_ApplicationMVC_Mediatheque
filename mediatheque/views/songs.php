<?php 

$base = dirname($_SERVER['PHP_SELF']); ?>

<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <title>Gestion des chansons</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 text-gray-800">

    <nav class="bg-blue-800 shadow p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-white">Chansons de l'album</h1>
    <a href="?action=dashboard" class="text-sm text-white hover:underline">← Retour au menu</a>
    </nav>

    <div class="max-w-3xl mx-auto p-6">
    <?php if ($flash = getFlash()): ?>
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <h2 class="text-lg font-semibold mb-2">Album : <?= htmlspecialchars($album['title']) ?> (<?= htmlspecialchars($album['author']) ?>)</h2>

    <form method="post" action="?action=add_song&album_id=<?= $album['id'] ?>" class="bg-white p-4 rounded shadow mb-6 space-y-4">
        <h3 class="text-md font-bold">Ajouter une chanson</h3>
        <div>
        <label class="block">Titre</label>
        <input name="title" required class="border p-2 rounded w-full">
        </div>
        <div>
        <label class="block">Durée (minutes)</label>
        <input name="duration" type="number" step="0.1" required class="border p-2 rounded w-full">
        </div>
        <div>
        <label class="block">Note (0 à 5)</label>
        <input name="rating" type="number" min="0" max="5" required class="border p-2 rounded w-full">
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Ajouter</button>
    </form>

    <h3 class="text-md font-bold mb-2">Liste des chansons</h3>
    <table class="w-full table-auto border-collapse bg-white shadow rounded overflow-hidden">
        <thead class="bg-gray-200">
        <tr>
            <th class="px-6 py-3 text-left text-gray-700 font-semibold">Titre</th>
            <th class="px-6 py-3 text-left text-gray-700 font-semibold">Durée</th>
            <th class="px-6 py-3 text-left text-gray-700 font-semibold">Note</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($album['songs'] as $song): ?>
            <tr class="hover:bg-gray-50">
            <td class="p-2 "><?= htmlspecialchars($song['title']) ?></td>
            <td class="p-2 "><?= $song['duration'] ?> min</td>
            <td class="p-2 "><?= $song['rating'] ?>/5</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

</body>
</html>
