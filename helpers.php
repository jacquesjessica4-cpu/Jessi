<?php
// app/helpers.php

// Échapper toute sortie HTML (prévention XSS)
function e($v){
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function start_session(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Messages flash : affichés une seule fois après une redirection
function set_flash($type, $message){
    start_session();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(){
    start_session();
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

// Protection CSRF sur les formulaires qui modifient des données
function csrf_token(){
    start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token){
    start_session();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Retrouve un lecteur par son email, ou le crée s'il n'existe pas encore.
 * Permet d'identifier un lecteur sans mot de passe : il suffit de
 * redonner le même email pour retrouver sa liste de lecture.
 */
function trouver_ou_creer_lecteur(PDO $pdo, string $nom, string $prenom, string $email): int {
    $stmt = $pdo->prepare('SELECT id FROM lecteurs WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $lecteur = $stmt->fetch();

    if ($lecteur) {
        // Met à jour le nom/prénom au cas où ils auraient changé
        $update = $pdo->prepare('UPDATE lecteurs SET nom = :nom, prenom = :prenom WHERE id = :id');
        $update->execute([':nom' => $nom, ':prenom' => $prenom, ':id' => $lecteur['id']]);
        return (int) $lecteur['id'];
    }

    $insert = $pdo->prepare('INSERT INTO lecteurs (nom, prenom, email) VALUES (:nom, :prenom, :email)');
    $insert->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email]);
    return (int) $pdo->lastInsertId();
}

/**
 * Retourne les informations du lecteur actuellement identifié dans cette
 * session (ou null si personne n'est identifié).
 */
function lecteur_courant(PDO $pdo): ?array {
    start_session();
    if (empty($_SESSION['lecteur_id'])) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT id, nom, prenom, email FROM lecteurs WHERE id = :id');
    $stmt->execute([':id' => $_SESSION['lecteur_id']]);
    $lecteur = $stmt->fetch();
    return $lecteur ?: null;
}
