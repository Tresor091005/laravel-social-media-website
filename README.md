dépendances ajoutées :
laravel breeze
spatie sluggable
headlessui
heroicons
ckeditor5

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
    - Le soft deletes d'un modèle a pour effet d'ajouter une colonne deleted_at à la table et ajoute une fonctionnalité de corbeille

9. CKEditor
    - installation de ckeditor pour vue (démarche sur la page officielle)
    - mettre à jour le css pour faire correspondre le style de l'écriture et du rendu de l'éditeur
    - PROBLEME : lors de la fermeture et de la rouverture de la même modal (PostModal > PostList) les données ne sont pas réinitialisés
    - PROBLEME : la condition du read more / read less par rapport aux listes

10. Attachments upload on post creation
    - Ajout de la preview et de l'upload de fichiers côté frontend (PostModal)
    - Ajout de la colonne size à la table post_attachments
    - Ajout des règles de validation à StorePostRequest
    - Mise à jour du PostAttachment (Model)
    - Ajout des fichiers cotés backend via le Controller
    - Création de PostAttachmentResource et modif de PostRessource
    - Ajustement du rendu de PostItem
    - TODO : Améliorer les visuels (icône type de fichier) lors de l'upload
    - TODO : Prendre en charge plus de types de fichiers (StorePostRequest)
    - TODO : Pour tous les formulaires tester et gérer les erreurs

11. Attachments on update
    - front : combiner la preview des nouveaux fichiers (attachmentFiles) et de ceux du post (post.attachments)
    - front : mettre en place la suppression d'un ancien fichier et l'abandon de cette requête
    - front : bonne utilisation de la methode PUT
    - backend : autorisation d'update et règles de validation (certains ajouts à celui de StorePostRequest)
    - backend : update en DB et en Storage 
    - Modification du model PostAttachment pour que synchroniser les suppressions DB - Storage
    - Création d'une route dédié au téléchargement des fichiers du disque avec un nom personnalisé (nom d'origine dans la DB)

12. Preview Post Attachments
    - Création d'un composant modal dédié au Preview
    - Transfert de données PostItem > PostList > AttachmentPreviewModal
    - A quoi sert réellement le computed(), emit(), le v-model et l'interaction pour un two-way binding ?

13. Show validation errors
    - Passer les extensions acceptés depuis le backend grace au HandleInertiaRequest
    - Completer les formulaires d'envois pour capturer et afficher les erreurs
    - Mettre en place un système de warning lors de l'upload de nouveaux fichiers

14. Configuration pour les uploads
    - Modifier le fichier php.ini pour augmenter : upload_max_filesize, max_file_uploads et post_max_size
    - changement des règles de validation du StorePostRequest
    - Mise à jour de l'affichage des erreurs 
