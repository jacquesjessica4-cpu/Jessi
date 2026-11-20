<?php
// livres_gestion.php — Gestion du catalogue (Read + accès Create/Update/Delete)
require_once __DIR__ . '/app/helpers.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$flash = get_flash();
$livres = $pdo->query('SELECT * FROM livres ORDER BY titre')->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestion des livres</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <h1>Gestion du catalogue</h1>

  <?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <p><a class="btn" href="livres_create.php">+ Ajouter un livre</a></p>

  <table>
    <tr><th>Titre</th><th>Auteur</th><th>Maison d'édition</th><th>Exemplaires</th><th>Actions</th></tr>
    <?php foreach ($livres as $l): ?>
      <tr>
        <td><a href="details.php?id=<?= e($l['id']) ?>"><?= e($l['titre']) ?></a></td>
        <td><?= e($l['auteur']) ?></td>
        <td><?= e($l['maison_edition'] ?: '—') ?></td>
        <td><?= e($l['nombre_exemplaire']) ?></td>
        <td>
          <a href="livres_edit.php?id=<?= e($l['id']) ?>">Modifier</a>
          —
          <a href="livres_delete.php?id=<?= e($l['id']) ?>">Supprimer</a>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($livres)): ?>
      <tr><td colspan="5">Aucun livre dans le catalogue pour le moment.</td></tr>
    <?php endif; ?>
  </table>

  <p><a class="btn secondary" href="index.php">← Retour à l'accueil</a></p>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
