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

4. Frontend : Definition de la structure de la page principale
    - Défilement multiple et responsivité
   
5. Frontend : User profile page
   - point important > faire attention au rendu des utilisateurs non connectés
   
6. Upload de l'avatar et de la couverture du profile page : 
   - lecture d'un fichier niveau frontend
   - Utilisation d'un UserResource pour personnaliser les données envoyés au frontend depuis le backend
   - Gestion de la preview d'image avec FileReader (js)
   - Utilisation d'un formulaire avec Inertia Js (vue 3) : useForm and errors
   - PROBLEME : Recuperation des urls des fichiers(images) supprimés (dans UserResource)

7. Création d'un post et affichage
   - Modification du Model Post pour y établir les relations
   - Affichage de la liste de post à partir d'une collection de PostRessource
    
8. Mise à jour et suppression d'un post
    - Mise en place d'une boite modale PostModal et découverte de fonction Vuejs : emit, computed, watch
    - Un oeil sur la nomenclature et la methode des routes (dans ce cas l'update qui se fait par PUT)
        et l'autorisation sur la requête de mise à jour
