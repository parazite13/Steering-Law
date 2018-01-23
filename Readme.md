Etude de la loi comportementale de Steering
===========================================

Cette application a pour but de montrer empiriquement la loi de Steering qui décrit le temps nécessaire pour parcourir un certain trajet en fonction de la longueur et de la largeur de ce dernier selon la formule: 

`T = a * ID + b`

Avec 
* T le temps
* ID l'indice de difficultée d'un chemin : `ID = Longueur / Largueur`
* a et b des coefficients variables

Il s'agit donc de générer des chemins construit à partir de primitives prédéfinies et de les parcourir tout en mesurant le temps néncessaire.


Pré-Requis
----------

* Apache 2
* MongoDB v3.4 ou supérieur
* PHP v7.0 ou supérieur avec le driver mongodb.so

L'ensemble de ces pré-requis peuvent être installé via [AMPPS](http://www.ampps.com/download)


Installation
------------

* Créer un vhost Apache pointant vers la racine du projet
	* Editer le fichier `apache2.x.yy/conf/extra/httpd-vhosts/conf`
	* Ajouter ceci à la fin en remplacant SERVER_NAME par un nom de domaine (ex: steering, www.steering-law, www.steering_law.com ...) et PROJECT_FOLDER par le chemin vers le dossier contenant le projet
	`<VirtualHost *:80>
		ServerName SERVER_NAME
		DocumentRoot "PROJECT_FOLDER"
		<Directory  "PROJECT_FOLDER">
			Options +Indexes +Includes +FollowSymLinks +MultiViews
			AllowOverride All
			Require local
		</Directory>
	</VirtualHost>`
	* Editer le fichier `etc/host` et y ajouter en remplcant SERVER_NAME par le même nom utilisé précedemment
	`127.0.0.1	SERVER_NAME
	::1	SERVER_NAME`
* Configurer le projet en editant le fichier `include/config.php`, il faut renseigner pour la constante ABSURL la valeur précédemment utilisé pour SERVER_NAME en lui préfixant 'http://' et en lui suffixant un slash terminal (ex: http://steering/, http://www.steering-law/, http://www.steering_law.com/ ...).

Une fois l'installation terminée, vous pouvez accèder à l'application via l'URL précédemment renseignée depuis votre navigateur web favoris en vérifiant que le serveur web et la base de données sont et bien éxecuté.

Usage
------

L'interface se décompose en 2 partie :
* Utilisateur
* Administrateur

Lorsqu'on arrive sur l'application on est par défaut un utilisateur c'est à dire que l'on uniquement la possibilité de tester les expériences proposées par l'administrateur.
Pour cela il suffit de cliquer sur le bouton "Démarrer" après avoir réaliser ou non des entrainements via le boutton "S'entrainer"

Pour se connecter en tant qu'administrateur, il faut cliquer sur le bouton en haut a gauche de la page. Il faudra ensuite entrer le mot de passe tel qu'il a été définie dans le fichier `include/config.php` (par défaut 'admin').

Une fois administrateur, il est possible :
* De visualiser l'ensemble des chemins préalablement créé, d'en modifier l'order et en définissant son utilisation dans l'expérience courante ou non
* De créer une nouvelle expérience en utilisant une ou plusieurs pritives de base
* De visualiser les temps obtenu par les participants pour chacun des chemins qu'ils ont réalisé durant l'expérience
* D'afficher le graphique du temps en fonction de l'indice de difficultée pour chacun des chemins courant
