-- sql/donnees.sql
USE bibliotheque_en_ligne;

INSERT INTO livres (titre, auteur, description, maison_edition, nombre_exemplaire) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupéry', 'Un aviateur en panne dans le désert rencontre un petit garçon venu d''une autre planète.', 'Gallimard', 4),
('Vingt mille lieues sous les mers', 'Jules Verne', 'Les aventures du capitaine Nemo à bord du Nautilus.', 'Hetzel', 2),
('Fondation', 'Isaac Asimov', 'Le début de la saga qui imagine l''effondrement puis la renaissance d''un empire galactique.', 'Gnome Press', 3),
('Notre-Dame de Paris', 'Victor Hugo', 'L''histoire d''Esmeralda et de Quasimodo dans le Paris du Moyen Âge.', 'Gosselin', 0),
('L''Étranger', 'Albert Camus', 'Meursault, un homme indifférent, face à l''absurdité de l''existence.', 'Gallimard', 5),
('Une brève histoire du temps', 'Stephen Hawking', 'Une introduction accessible à la cosmologie moderne.', 'Bantam Books', 1),
('Les Misérables', 'Victor Hugo', 'Le parcours de Jean Valjean dans la France du XIXe siècle.', 'A. Lacroix', 2),
('La Ferme des animaux', 'George Orwell', 'Une fable politique où des animaux prennent le contrôle de leur ferme.', 'Secker & Warburg', 0);
