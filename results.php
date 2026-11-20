<?php
// results.php — Page de résultats de recherche
require_once __DIR__ . '/app/helpers.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$recherche = trim($_GET['q'] ?? '');
$resultats = [];

if ($recherche !== '') {
    // Requête préparée avec LIKE : anti-injection SQL même sur une recherche partielle
    $stmt = $pdo->prepare(
        'SELECT id, titre, auteur, nombre_exemplaire
         FROM livres
         WHERE titre LIKE :recherche OR auteur LIKE :recherche2
         ORDER BY titre'
    );
    $motif = '%' . $recherche . '%';
    $stmt->execute([':recherche' => $motif, ':recherche2' => $motif]);
    $resultats = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Résultats de recherche</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <h1>Résultats de recherche</h1>

  <form class="recherche" action="results.php" method="get" role="search">
    <input type="search" name="q" value="<?= e($recherche) ?>" placeholder="Titre ou nom d'auteur..." aria-label="Recherche">
    <button class="btn" type="submit">Rechercher</button>
  </form>

  <?php if ($recherche === ''): ?>
    <p class="hint">Saisissez un titre ou un nom d'auteur pour lancer une recherche.</p>
  <?php elseif (empty($resultats)): ?>
    <p class="hint">Aucun livre ne correspond à « <?= e($recherche) ?> ».</p>
  <?php else: ?>
    <p class="hint"><?= count($resultats) ?> résultat(s) pour « <?= e($recherche) ?> ».</p>
    <div class="grille-livres">
      <?php foreach ($resultats as $livre): ?>
        <div class="carte-livre">
          <h3><?= e($livre['titre']) ?></h3>
          <div class="auteur">par <?= e($livre['auteur']) ?></div>
          <div class="dispo <?= $livre['nombre_exemplaire'] > 0 ? 'oui' : 'non' ?>">
            <?= $livre['nombre_exemplaire'] > 0 ? $livre['nombre_exemplaire'] . ' exemplaire(s) disponible(s)' : 'Aucun exemplaire disponible' ?>
          </div>
          <div class="actions">
            <a class="btn" href="details.php?id=<?= e($livre['id']) ?>">Voir les détails</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <p><a class="btn secondary" href="index.php">← Retour à l'accueil</a></p>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
