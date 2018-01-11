````
Jeu de cartes
````


Exécution du projet
------------------
````
 Le projet s'exécute en ligne de commande ( mode console ) :
     
    - Se placer à la racine du projet 
    
    - Lancer la commande : php public/index.php sort_cards


Notes
------------------
````
  - Le fichier .gitignore est laissé vide pour que tous les fichiers puissent être commités et que le correcteur puisse avoir le projet qui marche sur sa machine.
  - Sinon le dossier ./vendor devrait être dans ce fichier



Organisation des fichiers  + doc
--------------------------------
````
  - Le code de résolution du problème se trouve dans le controller IndexController
  - L'action principale traitant l'exercice s'intitule "sortCardsAction"
  - La "route" est défini dans le fichier ./module/Application/config/module.config.php dans la partie "console" 
