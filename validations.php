<?php
// app/validations.php

function validate_livre($data){
    $errors = [];

    $titre          = trim($data['titre'] ?? '');
    $auteur         = trim($data['auteur'] ?? '');
    $description    = trim($data['description'] ?? '');
    $maisonEdition  = trim($data['maison_edition'] ?? '');
    $nombreExemplaire = trim($data['nombre_exemplaire'] ?? '');

    if ($titre === '' || mb_strlen($titre) > 100) {
        $errors['titre'] = 'Titre obligatoire (100 caractères maximum).';
    }
    if ($auteur === '' || mb_strlen($auteur) > 100) {
        $errors['auteur'] = 'Auteur obligatoire (100 caractères maximum).';
    }
    if (mb_strlen($maisonEdition) > 100) {
        $errors['maison_edition'] = 'Maison d\'édition trop longue (100 caractères maximum).';
    }
    if ($nombreExemplaire === '' || !ctype_digit($nombreExemplaire) || (int)$nombreExemplaire < 0) {
        $errors['nombre_exemplaire'] = 'Nombre d\'exemplaires invalide (entier positif ou nul).';
    }

    return [$errors, [
        'titre' => $titre,
        'auteur' => $auteur,
        'description' => $description,
        'maison_edition' => $maisonEdition,
        'nombre_exemplaire' => $nombreExemplaire,
    ]];
}

function validate_lecteur($data){
    $errors = [];

    $nom = trim($data['nom'] ?? '');
    $prenom = trim($data['prenom'] ?? '');
    $email = trim($data['email'] ?? '');

    if ($nom === '' || mb_strlen($nom) > 100) {
        $errors['nom'] = 'Nom obligatoire (100 caractères maximum).';
    }
    if ($prenom === '' || mb_strlen($prenom) > 100) {
        $errors['prenom'] = 'Prénom obligatoire (100 caractères maximum).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse email invalide.';
    }

    return [$errors, ['nom' => $nom, 'prenom' => $prenom, 'email' => $email]];
}
