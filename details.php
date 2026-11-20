<?php
// details.php — Page de détails d'un livre
require_once __DIR__ . '/app/helpers.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM livres WHERE id = :id');
$stmt->execute([':id' => $id]);
$livre = $stmt->fetch();

if (!$livre) {
    set_flash('error', 'Livre introuvable.');
    header('Location: index.php');
    exit;
}

$lecteurActuel = lecteur_courant($pdo);
$dejaDansListe = false;

if ($lecteurActuel) {
    $stmtCheck = $pdo->prepare('SELECT 1 FROM liste_lecture WHERE id_livre = :id_livre AND id_lecteur = :id_lecteur');
    $stmtCheck->execute([':id_livre' => $id, ':id_lecteur' => $lecteurActuel['id']]);
    $dejaDansListe = (bool) $stmtCheck->fetchColumn();
}

// Traitement de l'ajout à la liste de lecture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter_liste') {

    if (!csrf_check($_POST['csrf'] ?? '')) {
        set_flash('error', 'Formulaire invalide, merci de réessayer.');
        header('Location: details.php?id=' . $id);
        exit;
    }

    if (!$lecteurActuel) {
        set_flash('error', 'Merci de vous identifier avant d\'ajouter un livre à votre liste.');
        header('Location: lecteur_identification.php?retour=details.php%3Fid%3D' . $id);
        exit;
    }

    if ($dejaDansListe) {
        set_flash('error', 'Ce livre est déjà dans votre liste de lecture.');
        header('Location: details.php?id=' . $id);
        exit;
    }

    if ((int) $livre['nombre_exemplaire'] <= 0) {
        set_flash('error', 'Aucun exemplaire disponible pour ce livre actuellement.');
        header('Location: details.php?id=' . $id);
        exit;
    }

    // Transaction : on insère l'emprunt ET on décrémente le stock ensemble,
    // pour que les deux opérations réussissent ou échouent ensemble.
    try {
        $pdo->beginTransaction();

        $insert = $pdo->prepare(
            'INSERT INTO liste_lecture (id_livre, id_lecteur, date_emprunt, date_retour)
             VALUES (:id_livre, :id_lecteur, CURDATE(), NULL)'
        );
        $insert->execute([':id_livre' => $id, ':id_lecteur' => $lecteurActuel['id']]);

        $update = $pdo->prepare('UPDATE livres SET nombre_exemplaire = nombre_exemplaire - 1 WHERE id = :id AND nombre_exemplaire > 0');
        $update->execute([':id' => $id]);

        if ($update->rowCount() === 0) {
            // Un autre lecteur a pris le dernier exemplaire entre-temps
            $pdo->rollBack();
            set_flash('error', 'Aucun exemplaire disponible pour ce livre actuellement.');
            header('Location: details.php?id=' . $id);
            exit;
        }

        $pdo->commit();
    } catch (Exception $ex) {
        $pdo->rollBack();
        set_flash('error', 'Une erreur est survenue, merci de réessayer.');
        header('Location: details.php?id=' . $id);
        exit;
    }

    set_flash('ok', 'Livre ajouté à votre liste de lecture.');
    header('Location: details.php?id=' . $id);
    exit;
}

$flash = get_flash();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($livre['titre']) ?> — Détails</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <h1><?= e($livre['titre']) ?></h1>

  <div class="fiche">
    <dl>
      <dt>Auteur</dt><dd><?= e($livre['auteur']) ?></dd>
      <dt>Maison d'édition</dt><dd><?= e($livre['maison_edition'] ?: '—') ?></dd>
      <dt>Exemplaires disponibles</dt>
      <dd class="dispo <?= $livre['nombre_exemplaire'] > 0 ? 'oui' : 'non' ?>">
        <?= $livre['nombre_exemplaire'] > 0 ? e($livre['nombre_exemplaire']) . ' disponible(s)' : 'Aucun exemplaire disponible' ?>
      </dd>
      <dt>Description</dt><dd><?= nl2br(e($livre['description'] ?: 'Aucune description disponible.')) ?></dd>
    </dl>

    <?php if ($dejaDansListe): ?>
      <p class="alert ok">Ce livre est déjà dans votre liste de lecture.</p>
    <?php elseif (!$lecteurActuel): ?>
      <p class="hint">
        <a href="lecteur_identification.php?retour=details.php%3Fid%3D<?= e($id) ?>">Identifiez-vous</a>
        pour ajouter ce livre à votre liste de lecture.
      </p>
    <?php else: ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="ajouter_liste">
        <button class="btn" type="submit" <?= $livre['nombre_exemplaire'] <= 0 ? 'disabled' : '' ?>>
          Ajouter à ma liste de lecture
        </button>
      </form>
    <?php endif; ?>
  </div>

  <p style="margin-top:16px;"><a class="btn secondary" href="index.php">← Retour à l'accueil</a></p>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
