Php version
laravel version
ide and version
base de données

dépendances ajoutées : 
    laravel breeze
    spatie sluggable

outils utilisés :
    ide
    navigateur
    mailpit

1. Initialisation du projet avec breeze

2. Création des models et migration
   Post, PostReaction, PostAttachment, Comment, Group, GroupUser, Follower, User (déjà présent; ajout)

3. Génération d'un username unique à l'inscription
   a. Ajout de la fonctionnnalité MustVerifyEmail au model User et configuration de mailpit pour le projet
   b. utilisation de spatie sluggable dans le model User pour générer le champ username dynamiquement lors de l'inscription uniquement
   c. Gérer le changement manuel du username dans la page d'éditon du profile (ajout du champ, sauvegarde en DB, personnalisation des règles)
