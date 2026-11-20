<?php
// index.php — Page d'accueil (correspond à "index.html" de l'énoncé ;
// voir README pour l'explication du choix de l'extension .php).
require_once __DIR__ . '/app/helpers.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$flash = get_flash();
$totalLivres = (int) $pdo->query('SELECT COUNT(*) FROM livres')->fetchColumn();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bibliothèque en Ligne — Accueil</title>
  <meta name="description" content="Recherchez, consultez et empruntez des livres depuis notre bibliothèque en ligne.">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <div class="hero">
    <h1>Bienvenue à la Bibliothèque en Ligne</h1>
    <p>
      Notre catalogue compte actuellement <strong><?= e($totalLivres) ?></strong> livre(s).
      Recherchez un titre ou un auteur ci-dessous, consultez la fiche détaillée d'un livre,
      puis ajoutez-le à votre liste de lecture pour le retrouver facilement.
    </p>
  </div>

  <h2>Comment utiliser le site ?</h2>
  <ol>
    <li>Recherchez un livre par titre ou par auteur dans le champ ci-dessous.</li>
    <li>Cliquez sur « Voir les détails » pour consulter sa fiche complète.</li>
    <li>Identifiez-vous (nom, prénom, email — aucun mot de passe requis) pour l'ajouter à votre liste de lecture.</li>
    <li>Retrouvez et gérez vos emprunts depuis « Ma liste de lecture ».</li>
  </ol>

  <h2>Rechercher un livre</h2>
  <form class="recherche" action="results.php" method="get" role="search">
    <input type="search" name="q" placeholder="Titre ou nom d'auteur..." aria-label="Recherche">
    <button class="btn" type="submit">Rechercher</button>
  </form>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
