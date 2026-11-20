<?php
// lecteur_identification.php — Identifie ou crée un lecteur (nom, prénom, email)
require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/validations.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$retour = $_GET['retour'] ?? $_POST['retour'] ?? 'index.php';
// Sécurité simple : on n'autorise la redirection que vers une page locale du site
if (!preg_match('/^[a-zA-Z0-9_\.\-]+\.php(\?[a-zA-Z0-9_=&%]*)?$/', $retour)) {
    $retour = 'index.php';
}

$errors = [];
$values = ['nom' => '', 'prenom' => '', 'email' => ''];
$lecteurActuel = lecteur_courant($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrf_check($_POST['csrf'] ?? '')) {
        set_flash('error', 'Formulaire invalide, merci de réessayer.');
        header('Location: lecteur_identification.php');
        exit;
    }

    [$errors, $values] = validate_lecteur($_POST);

    if (empty($errors)) {
        $idLecteur = trouver_ou_creer_lecteur($pdo, $values['nom'], $values['prenom'], $values['email']);
        $_SESSION['lecteur_id'] = $idLecteur;
        set_flash('ok', 'Vous êtes identifié·e comme ' . $values['prenom'] . '.');
        header('Location: ' . $retour);
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Identification lecteur</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/app.js" defer></script>
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <h1>Identification lecteur</h1>
  <p class="hint">
    Aucun mot de passe n'est nécessaire : indiquez simplement votre nom, prénom et email.
    Si vous revenez plus tard avec le même email, votre liste de lecture sera retrouvée automatiquement.
  </p>

  <?php if (!empty($errors)): ?>
    <div class="alert error">Veuillez corriger les erreurs ci-dessous.</div>
  <?php endif; ?>

  <?php if ($lecteurActuel): ?>
    <p class="alert ok">
      Actuellement identifié·e comme <?= e($lecteurActuel['prenom']) ?> <?= e($lecteurActuel['nom']) ?>
      (<?= e($lecteurActuel['email']) ?>). Vous pouvez changer d'identité ci-dessous.
    </p>
  <?php endif; ?>

  <form class="form-lecteur" method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="retour" value="<?= e($retour) ?>">

    <label>Nom</label>
    <input name="nom" value="<?= e($values['nom']) ?>">
    <?php if (isset($errors['nom'])) echo '<small style="color:#b00020">'.e($errors['nom']).'</small>'; ?>

    <label>Prénom</label>
    <input name="prenom" value="<?= e($values['prenom']) ?>">
    <?php if (isset($errors['prenom'])) echo '<small style="color:#b00020">'.e($errors['prenom']).'</small>'; ?>

    <label>Email</label>
    <input type="email" name="email" value="<?= e($values['email']) ?>">
    <?php if (isset($errors['email'])) echo '<small style="color:#b00020">'.e($errors['email']).'</small>'; ?>

    <button class="btn" type="submit">Valider</button>
    <a class="btn secondary" href="index.php">Retour</a>
  </form>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
