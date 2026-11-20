<?php
// livres_create.php — Create
require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/validations.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$errors = [];
$values = ['titre' => '', 'auteur' => '', 'description' => '', 'maison_edition' => '', 'nombre_exemplaire' => '0'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_check($_POST['csrf'] ?? '')) {
        set_flash('error', 'Formulaire invalide, merci de réessayer.');
        header('Location: livres_create.php');
        exit;
    }

    [$errors, $values] = validate_livre($_POST);

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO livres (titre, auteur, description, maison_edition, nombre_exemplaire)
             VALUES (:titre, :auteur, :description, :maison_edition, :nombre_exemplaire)'
        );
        $stmt->execute([
            ':titre' => $values['titre'],
            ':auteur' => $values['auteur'],
            ':description' => $values['description'] ?: null,
            ':maison_edition' => $values['maison_edition'] ?: null,
            ':nombre_exemplaire' => $values['nombre_exemplaire'],
        ]);

        set_flash('ok', 'Livre ajouté au catalogue.');
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
  <title>Ajouter un livre</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/app.js" defer></script>
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <h1>Ajouter un livre</h1>

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

    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn secondary" href="livres_gestion.php">Retour</a>
  </form>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
