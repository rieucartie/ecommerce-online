# complet-ecommerce-symfony

# La démo est sur youtube :

https://youtu.be/LgIUQSHTI2c

composer install

composer require symfony/webpack-encore-bundle

yarn install

yarn run dev

si besoin : npm i -S webpack@latest ou yarn add webpack@latest

php bin/console d:d:c

php bin/console make:migration

php bin/console doctrine:migrations:migrate

creez un dossier uploads pour y poser les photos des produits dans le dossier public

# Pour les fixtures : 

    - soit avec les fixtures yml dans ce cas lancez :
    
       * Pensez à modifier l'encodage du password du user selon votre choix de hashage
        php bin/console hautelook:fixtures:load

    - soit avec les DataFixtures dans ce cas à vous de faire 

        php bin/console d:f:l

    - soit avec un insert sql à la racine à completer  


    
