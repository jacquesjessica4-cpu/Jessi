<?php
// wishlist.php — Page de liste de lecture
require_once __DIR__ . '/app/helpers.php';
$pdo = require __DIR__ . '/app/db.php';
start_session();

$lecteurActuel = lecteur_courant($pdo);

// Traitement du retrait d'un livre de la liste
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'retirer') {

    if (!csrf_check($_POST['csrf'] ?? '')) {
        set_flash('error', 'Formulaire invalide, merci de réessayer.');
        header('Location: wishlist.php');
        exit;
    }

    if (!$lecteurActuel) {
        header('Location: lecteur_identification.php');
        exit;
    }

    $idLivre = (int) ($_POST['id_livre'] ?? 0);

    try {
        $pdo->beginTransaction();

        $delete = $pdo->prepare('DELETE FROM liste_lecture WHERE id_livre = :id_livre AND id_lecteur = :id_lecteur');
        $delete->execute([':id_livre' => $idLivre, ':id_lecteur' => $lecteurActuel['id']]);

        if ($delete->rowCount() > 0) {
            // Le livre redevient disponible pour un autre lecteur
            $update = $pdo->prepare('UPDATE livres SET nombre_exemplaire = nombre_exemplaire + 1 WHERE id = :id');
            $update->execute([':id' => $idLivre]);
        }

        $pdo->commit();
        set_flash('ok', 'Livre retiré de votre liste de lecture.');
    } catch (Exception $ex) {
        $pdo->rollBack();
        set_flash('error', 'Une erreur est survenue, merci de réessayer.');
    }

    header('Location: wishlist.php');
    exit;
}

$flash = get_flash();
$livresDeLaListe = [];

if ($lecteurActuel) {
    $stmt = $pdo->prepare(
        'SELECT l.id, l.titre, l.auteur, ll.date_emprunt
         FROM liste_lecture ll
         JOIN livres l ON l.id = ll.id_livre
         WHERE ll.id_lecteur = :id_lecteur
         ORDER BY ll.date_emprunt DESC'
    );
    $stmt->execute([':id_lecteur' => $lecteurActuel['id']]);
    $livresDeLaListe = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ma liste de lecture</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container">
  <h1>Ma liste de lecture</h1>

  <?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <?php if (!$lecteurActuel): ?>
    <p class="hint">
      <a href="lecteur_identification.php?retour=wishlist.php">Identifiez-vous</a>
      pour voir et gérer votre liste de lecture.
    </p>
  <?php elseif (empty($livresDeLaListe)): ?>
    <p class="hint">Votre liste de lecture est vide pour le moment. Recherchez un livre et ajoutez-le depuis sa fiche détaillée.</p>
  <?php else: ?>
    <table>
      <tr><th>Titre</th><th>Auteur</th><th>Emprunté le</th><th>Action</th></tr>
      <?php foreach ($livresDeLaListe as $l): ?>
        <tr>
          <td><a href="details.php?id=<?= e($l['id']) ?>"><?= e($l['titre']) ?></a></td>
          <td><?= e($l['auteur']) ?></td>
          <td><?= e($l['date_emprunt']) ?></td>
          <td>
            <form method="post" style="margin:0;">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="retirer">
              <input type="hidden" name="id_livre" value="<?= e($l['id']) ?>">
              <button class="btn secondary" type="submit">Retirer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <p><a class="btn secondary" href="index.php">← Retour à l'accueil</a></p>
</div>

<div class="footer">Bibliothèque en Ligne — Projet final, Développement Web</div>
</body>
</html>
