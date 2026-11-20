<?php
// livres_delete.php — Delete (avec confirmation, exécution en POST uniquement)
require_once __DIR__ . '/app/helpers.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT titre FROM livres WHERE id = :id');
$stmt->execute([':id' => $id]);
$livre = $stmt->fetch();

if (!$livre) {
    set_flash('error', 'Livre introuvable.');
    header('Location: livres_gestion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        set_flash('error', 'Formulaire invalide, merci de réessayer.');
        header('Location: livres_gestion.php');
        exit;
    }

    // ON DELETE CASCADE sur liste_lecture : les emprunts liés à ce livre
    // sont supprimés automatiquement en même temps (cohérence référentielle).
    $stmt = $pdo->prepare('DELETE FROM livres WHERE id = :id');
    $stmt->execute([':id' => $id]);

    set_flash('ok', 'Livre supprimé du catalogue.');
    header('Location: livres_gestion.php');
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Supprimer un livre</title>
<link rel="stylesheet" href="assets/style.css"></head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <h1>Confirmation de suppression</h1>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <p>Confirmer la suppression du livre « <?= e($livre['titre']) ?> » ?</p>
    <p class="hint">Cette action retirera aussi ce livre des listes de lecture des lecteurs concernés.</p>
    <button class="btn danger" type="submit">Oui, supprimer</button>
    <a class="btn secondary" href="livres_gestion.php">Annuler</a>
  </form>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
