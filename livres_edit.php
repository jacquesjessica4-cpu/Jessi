<?php
// livres_edit.php — Update
require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/validations.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM livres WHERE id = :id');
$stmt->execute([':id' => $id]);
$livre = $stmt->fetch();

if (!$livre) {
    set_flash('error', 'Livre introuvable.');
    header('Location: livres_gestion.php');
    exit;
}

$errors = [];
$values = [
    'titre' => $livre['titre'],
    'auteur' => $livre['auteur'],
    'description' => $livre['description'],
    'maison_edition' => $livre['maison_edition'],
    'nombre_exemplaire' => $livre['nombre_exemplaire'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_check($_POST['csrf'] ?? '')) {
        set_flash('error', 'Formulaire invalide, merci de réessayer.');
        header('Location: livres_edit.php?id=' . $id);
        exit;
    }

    [$errors, $values] = validate_livre($_POST);

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'UPDATE livres SET titre=:titre, auteur=:auteur, description=:description,
             maison_edition=:maison_edition, nombre_exemplaire=:nombre_exemplaire WHERE id=:id'
        );
        $stmt->execute([
            ':titre' => $values['titre'],
            ':auteur' => $values['auteur'],
            ':description' => $values['description'] ?: null,
            ':maison_edition' => $values['maison_edition'] ?: null,
            ':nombre_exemplaire' => $values['nombre_exemplaire'],
            ':id' => $id,
        ]);

        set_flash('ok', 'Livre mis à jour.');
        header('Location: livres_gestion.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modifier un livre</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/app.js" defer></script>
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <h1>Modifier « <?= e($livre['titre']) ?> »</h1>

  <?php if (!empty($errors)): ?>
    <div class="alert error">Veuillez corriger les erreurs ci-dessous.</div>
  <?php endif; ?>

  <form class="form-livre" method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <label>Titre</label>
    <input name="titre" value="<?= e($values['titre']) ?>">
    <?php if (isset($errors['titre'])) echo '<small style="color:#b00020">'.e($errors['titre']).'</small>'; ?>

    <label>Auteur</label>
    <input name="auteur" value="<?= e($values['auteur']) ?>">
    <?php if (isset($errors['auteur'])) echo '<small style="color:#b00020">'.e($errors['auteur']).'</small>'; ?>

    <label>Description</label>
    <textarea name="description"><?= e($values['description']) ?></textarea>

    <label>Maison d'édition</label>
    <input name="maison_edition" value="<?= e($values['maison_edition']) ?>">
    <?php if (isset($errors['maison_edition'])) echo '<small style="color:#b00020">'.e($errors['maison_edition']).'</small>'; ?>

    <label>Nombre d'exemplaires</label>
    <input type="number" min="0" name="nombre_exemplaire" value="<?= e($values['nombre_exemplaire']) ?>">
    <?php if (isset($errors['nombre_exemplaire'])) echo '<small style="color:#b00020">'.e($errors['nombre_exemplaire']).'</small>'; ?>

    <button class="btn" type="submit">Mettre à jour</button>
    <a class="btn secondary" href="livres_gestion.php">Retour</a>
  </form>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
