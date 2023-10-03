# Proto-elearning


## Commande pour lancer l'application
# Ouvrir un terminal
cd .\Proto-API\

docker build . -t symfony_proto

cd ..

docker-compose up --build

## Configurer la base de données
# Ouvrir le terminal du container
# Pas sûr
composer install

php bin/console doctrine:schema:update --force

php bin/console doctrine:fixtures:load

# PHPMyAdmin localhost:8080
# Serveur Symfony localhost:80