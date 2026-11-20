<?php
// partials/nav.php
// Attend que $pdo, start_session() et lecteur_courant() soient disponibles
// (chargés via app/helpers.php et app/db.php dans la page appelante).
$lecteurActuel = lecteur_courant($pdo);
?>
<nav class="nav">
  <a class="brand" href="index.php"><span class="logo">◆</span> Bibliothèque en Ligne</a>
  <div class="liens">
    <a href="index.php">Accueil</a>
    <a href="livres_gestion.php">Gérer les livres</a>
    <a href="wishlist.php">Ma liste de lecture</a>
  </div>
  <div class="lecteur">
    <?php if ($lecteurActuel): ?>
      Identifié·e comme <strong><?= e($lecteurActuel['prenom']) ?></strong>
      — <a href="lecteur_identification.php">changer</a>
    <?php else: ?>
      <a href="lecteur_identification.php">S'identifier comme lecteur</a>
    <?php endif; ?>
  </div>
</nav>
