// assets/app.js
// Validation client basique : améliore l'expérience utilisateur, mais la
// validation côté serveur (app/validations.php) reste la seule à faire foi.
document.addEventListener('DOMContentLoaded', () => {

    // Formulaire de gestion des livres (création / modification)
    const formLivre = document.querySelector('form.form-livre');
    if (formLivre) {
        formLivre.addEventListener('submit', (e) => {
            const titre = formLivre.querySelector('[name="titre"]');
            const auteur = formLivre.querySelector('[name="auteur"]');
            if (titre && titre.value.trim() === '') {
                alert('Le titre est obligatoire.');
                e.preventDefault();
                return;
            }
            if (auteur && auteur.value.trim() === '') {
                alert('L\'auteur est obligatoire.');
                e.preventDefault();
            }
        });
    }

    // Formulaire d'identification du lecteur
    const formLecteur = document.querySelector('form.form-lecteur');
    if (formLecteur) {
        formLecteur.addEventListener('submit', (e) => {
            const email = formLecteur.querySelector('[name="email"]');
            if (email && !email.value.includes('@')) {
                alert('Merci de saisir une adresse email valide.');
                e.preventDefault();
            }
        });
    }
});
