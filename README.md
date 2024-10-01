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

15. Réaction sur les posts
    Ici on fait face à la problématique d'envoyer des requêtes au backend sans perdre l'affichage du frontend actuel
    - Mise en place axios
    - backend : 
        - Enum pour la règle de validation concernant les types de reactions
        - Models Post(relation) et PostReactions(fillable)
        - Resource Post pour passer le nbr total de réactions et la présence de l'utilisateur
        - HomeController pour ajouter la récupération du nombre total de réactions et la collection de la relation 'réactions' du modèle Post paramétrée sur l'utilisateur connecté
        - PostController pour ajouter la méthode pour une réaction et renvoyant correctment le nbr total de réactions et la présence de l'utilisateur pour le post visé
    - frontend :
        - Utilisation de axios pour contacter le backend et mettre à jour les données du post(frontend)

    
    Pour implémenter plusieurs types de réaction
        - Enum : ennumérer les types de réactions
        - Model Post : relation personnalisé par type avec la table post_réaction
        - Modification du front(PostItem) et de PostResource qui fournit les informations sur le Post
        - Adaptation du HomeController pour aider le PostRessource
        - Modification de la logique du PostController pour gérer au mieux les changements et les renvoyés au front
        - Utilisation de axios pour mettre à jour les données côté front

16. Création de commentaires sur les postes

    - frontend :
        - Utilisation d'un Disclosure Panel pour créer une zone réservés aux commentaires
        - Affichage des commentaires (notamment avec un ReadMoreLess)
        - TextArea pour saisir le nouveau commentaire à créer

    - backend :
        - Création d'une route et d'une méthode de controller pour permettre la création d'un commentaire
        - Mise à jour des models Post et Comment pour permettre l'enrégistrement de données (Model::create([])) et relations avec les autres modelès
        - Mise à jour PostResource / Création CommentaireResource pour fournir les données nécéssaires aux Commentaires sur les Postes
        - Intégration de la liste des commentaires et du nombre dans les données envoyés via le HomeController

    PROBLEME : impossible de télécharger tous les fichiers attachés à un post lorsque leur nombre depasse 4

17. Mise à jour et supression des commentaires

    - frontend: 
        - creation d'un composant dropdown, d'une section d'édition de commentaire
        - mise en place des fonctions au clics d'éditon/supression qui modifie convenablement en retour la liste des commentaires
    - backend: 
        - création des routes et méthodes d'édition/supression de commentaire (en veillant sur l'autorisation)

    PROBLEME : N'importe qui peut télécharger n'importe quel fichier attaché à un post en tapant son url

18. Implementer les réactions sur les commentaires
    - Modification de la table post_reactions en reactions
    - Relation Polymorphique entre Reaction - Post/Comment (One to Many)
    - Fixer les bugs créés
    - Implementer les réactions sur les commentaires (mentions spécial au chargement du nombre de réactions sur chaque commentaire d'un post en parallèles à celui du post lui-même xD)

    NOTE : Il n'est pas conseillé d'utiliser les modèles dans les migrations

19. Ecrire des sous commentaires

    NOTE : Dans le cas d'une relation avec lui même, la nouvelle colonne de liaison doit avoir les mêmes propriétés que la colonne référence (table comments : id - parent_id)

    - frontend : 
        - Création d'un composant CommentList pour afficher la liste des commentaires 
            Ce composant à un caractère récursif afin d'afficher la liste des sous commentaires
    
        - NOTE : on ne peut modifier un props.Array mais on peut modifier les propriétés d'un props.Object
    
    - backend : 
        - Mise à jour du chargement et de la gestion (création, update, delete) des Commentaires
        
20. 

