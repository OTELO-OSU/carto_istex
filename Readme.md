# Projet Cartographie Istex usage OTELo

**Présentation et objectifs**

Réaliser des cartographies d'informations à partir du réservoir de publications ISTEX 

Cartographies des : 
			
	- auteurs.
			
	- Institutions/laboratoires
	
	- Pays 

issus d'une recherche X. 

**But**

Ce projet permettra de compléter le travail bibliographique des chercheurs, doctorants et post-doctorants impliqués dans leur projets.
Dans le cadre du financement du projet ISTEX-Usage OTELo, ces cartographies permettront d'apporter un complément bibliographique sur les terres rares du sol à l'atmosphère, et des éléments de réponses complémentaires sur:

La connaissance des processus géologiques intervenant dans l'enrichissement des terres rares,
l’impact des terres rares sur l'écosphère terrestre et aquatique.


**Introduction**

L’application Carto Istex permet la réalisation de cartographie d'informations à partir du réservoir de publications ISTEX suivant leurs provenances (Pays, Laboratoire) et leurs auteurs.
Elle est composée d’un backend codé en PHP avec le micro-framework SLIM ainsi que d’un frontend codé en HTML,CSS ,JavaScript et de la librairie Jquery.


**Principe de fonctionnement :**

Un utilisateur effectue une recherche (query) dans le champ prévu à cet effet. Champ issu de l'intégration des widgets Istex-SNU.
Différents appels POST AJAX, avec en paramètre la "query", vont être envoyés vers le backend.
Une réponse de l'api ISTEX au format JSON va être retournée contenant les informations à traiter et à afficher.
La réponse est traitée par les scripts JavaScript : getcountry pour les pays, getlaboratory pour les laboratoires ainsi que getauthor pour les auteurs.
L’utilisateur peut ensuite consulter les résultats sous forme de tableau dynamique et paginé, ou sous forme de carte exportable en format PDF et/ou PNG.



**Aspect général de l’application :**

Pour l’aspect, le framework Semantic UI a été choisi pour sa simplicité d’utilisation et sa bonne documentation. Il permet de réaliser des interfaces graphiques responsives légère et rapide. 
Pour afficher la carte des pays, la librairie Leaflet a été choisie, pour sa simplicité d’utilisation ainsi que sa légèreté. L’utilisateur à la possibilité d’exporter la carte générée au format PDF ou même de l’imprimer.
Afin de réaliser des Bubblecharts (Graphique à bulles) la librairie GoogleChart a été utilisée. L’utilisateur peut enregistrer le graphique généré au format PNG.
Le plugin Jquery Datatable a été utilisé pour rendre dynamique les tableaux ainsi la recherche dans les tableaux est facilitée. Il est même possible de trier ceux-ci par ordre alphabétique ou par nombre. Concernant le nombre de publications erronées (publication possédant une affiliation qui ne peut être traité correctement), il est affiché pour chaque catégories (pays, institution, auteur), le nombre diffère selon la catégorie, en effet une publication peut avoir un auteur mais une mauvaise affiliation.



![Alt text](/Img_doc/Accueil.png?raw=true)





# Langages et outils utilisés:

PHP
Memcached afin de gérer la mise en cache.
Activer l'extension memcached sur la machine hôte.

**Configuration de Memcached:**

Passer la taille d'un item à maximum 24MO. 


**Dépendances:**

	- Apache2

	- PHP 5.6:
		Librairie php: libmemcached (ne pas oublier d'activer l'extension dans la configuration php)
		Php Curl afin d'effectuer les requêtes sur l'API ISTEX
		Php pgsql pour accéder à la base pgsql de nominatim
		Pear 
		DB php

	-Memcached: Fichier à Modifier /etc/memcached.conf
	Il suffit de modifier les valeurs:

	Valeur minimum recommandé:
	Allocation de 2 Go a memcached:
	-m 2048
	Allocation de 24MO maximum par item
	-I 24M
	Lancement de memcached sur 20 threads
	-t 20


	- Python:

		- PIP

		- Installation de pylibmc (librairie python memcached)

	Afin que les scripts python se lancent convenablement il faut s'assurer que www-data ait les droits pour exécuter les scripts.


# Back-end:

**Organisation du code:**

	Backend_Istex_usage
		--src
			--istex 
				--backend/controller contient les controllers ainsi que les dépendances nominatim  
			--index.php : fichier de routes
			--Multiquery.py 
			--Requestprocessing.py 
		--vendor : contient les dépendances slim nécessaires au routage


**Détails des différentes classes et fonctions PHP:**

-**RequestController**: 
Cette classe va permettre d'effectuer les requêtes vers l'api Istex et récupérer les données nécessaires au tri.
Elle est composée de plusieurs fonctions:

	- CurlRequest() : Prend en paramètre l'url ainsi que les options de type CURL, elle retourne les données de l'api Istex au format JSON.

	- Request_alldoc_querypagebypage(): Elle prend en paramètre la requête (les termes de recherche de l'utilisateur),Cette fonction vérifie si une réponse à la requête est présente en cache, puis si aucun cache n'est présent, elle interroge l’API Istex. Les affiliations sont ensuite parsées et comparées à un dictionnaire de mots. Cette fonction retourne un tableau avec tous les documents qui possèdent une affiliation correcte.

	

-**LaboratoryController**:
Cette classe permet de rassembler les laboratoires entre eux et les compter.

-**AuthorController**:
Cette classe permet de rassembler les auteurs entre eux et les compter.



**Détails du script python Requestprocessing (permettant un tri des affiliations en multiprocessing)**


	- Main(): Fonction principale, découpage en tableau du json reçu, puis lancement du traitement des différents tableaux dans un process chacun.

	- split(): permet de découper un tableau python en plusieurs petits tableaux d'une taille donnée.

	- Processing(): Fonction de tri et de curation des affiliations, lancé en parallèle dans chaque process.

	- Match_result_for_laboratory():Elle prend en paramètre un tableau contenant un partie de l’affiliation. Elle compare avec le dictionnaire puis retourne le résultat si ok, sinon elle ne retourne rien.

	- Search_for_labo():Recherche dans l'affiliation qui ne possède pas d'institution, un deuxième laboratoire.

	- Search_for_university():Recherche dans l'affiliation qui ne possède pas de labo, une deuxième institution parmi la liste Institution-Major, si pas de réponse, on considère comme NULL.

	- Search_for_university_labo():Si on en trouve une, on la passe dans au tableau Institution-Labo, si OK, on affiche deux institutions différentes.

	- Search_for_university_labo_and_inst():Si pas de résultat dans Search_for_university_labo(), on recherche dans la liste Institution-institution, si OK, on affiche 2 fois la même institution.

	- Match_result_for_university():Elle prend en paramètre un tableau contenant les différentes partie de l’affiliation. Elle compare avec le dictionnaire (liste) puis retourne le résultat si OK, sinon elle ne retourne rien.

Les deux fonctions ci dessus permettent de s'assurer de la validité des affiliations.

**Détails du script Multiquery qui permet d'interroger le serveur Nominatim en local(Sans passer par une surcouche http comme le propose l'API de base):**

- split(): permet de découper un tableau python en plusieurs petits tableaux d'une taille donnée.

- Main(): Fonction principale, découpage en tableau du json reçu, afin de lancer les différents tableaux dans un process chacun.

- Processing(): Fonction d'interrogation de nominatim faisant appel à un script php afin d'interroger la librairie PHP de Nominatim, lancé en parallèle dans chaque process.


# Détails du traitement des affiliations:

**Présentation d’une affiliation :**

"Laboratoire de Géologie des bassins sédimentaires, Université Paris VI, 4 place Jussieu, Paris, France"

Certaines affiliations contiennent des point-virgules comme séparateur, on transforme donc ceux ci en virgules.Le même traitement est effectué pour les tirets.
L’affiliation est découpée en lots, ces lots sont « nettoyés » des caractères spéciaux et accents afin d’effectuer une comparaison optimale.

Le couple laboratoire/institution est obtenu avec différents traitements selon la forme d’écriture de l’affiliation.
Pour cela on utilise deux dictionnaires de données (Institution-All,Labo-All,voir le diagramme), un pour les laboratoires et l'autre pour les institutions.

Cas général :

En effet une affiliation peut avoir un couple laboratoire/institution, si les données contenue dans l’affiliation correspondent à celles contenues dans le dictionnaire de données , on obtient bien un couple laboratoire/institution.

Cas particuliers : 

Si une affiliation possèdent seulement un laboratoire une fois celle ci comparé avec le dictionnaire de données, pour les cas contenus dans le dictionnaire Labo-service, on affichera le laboratoire ainsi que dans le champs institution un nom considéré comme une institution.

**Exemple :**

Dans l’affiliation lors du passage avec les dictionnaires général, on obtient:
- LABORATORY FOR GEOCHEMICAL RESEARCH	
N’ayant pas d’institution, une recherche un autre laboratoire pouvant être considéré comme une institution , on obtient :
- HUNGARIAN ACADEMY OF SCIENCES
On va considérer que le terme academy est une institution.
On vérifie que le laboratoire n’est pas égal à l’institution.

Ce qui donnera :

Laboratoire :LABORATORY FOR GEOCHEMICAL RESEARCH	
Insitution :HUNGARIAN ACADEMY OF SCIENCES


Si une affiliation possède seulement une institution,on recherchera avec le dictionnaire (Institution-Major,dictionnaire d’institution confirmée); dans le cas ou un résultat ne correspond pas,on déclare vide le champs institution,sinon on le recherche dans le dictionnaire (Institution-Labo ,dictionnaires de nom pouvant être des laboratoires), si il y a une correspondance alors on affiche l’institution trouvée avec le dictionnaire général ainsi que la deuxième institution si elle n’est pas égal à la première.Si le résultat ne correspond toujours pas, alors on recherche dans le dictionnaire (Institution-institution, dictionnaires d’institution étant des laboratoires) et s’il y a une correspondance on affiche deux fois la même institution.

**Exemple :**

BRGM est une institution mais n’as pas de labo, on affichera :

Laboratoire : BRGM
Institution : BRGM

Pour le CNRS :
On ne pourra pas afficher les affiliations du CNRS ne contenant qu’une institution.

Pour un institut n’ayant pas de laboratoire :

Laboratoire : FACULTY OF SCIENCE	
Institution : GEOLOGICAL INSTITUTE	


**Diagramme:**

![Alt text](/Img_doc/Diagramme_Affiliation.png?raw=true)



# Fonctionnement de la librairie Nominatim:
Dans un objectif de gain de temps, l'interrogation de nominatim se fait par sa propre librairie,c'est à dire que la base de données Postgresql est interrogée directement par le script Sender_nominatim.php via la librairie Geocode de nominatim.
Pour faire simple, la surcouche API a été retiré.
Les résultats restent identiques, mais en enlevant la couche http, on constate une amélioration des temps de réponses.

**Fonctionnement de Geocode**

Premièrement , il a  fallu recompiler le projet Nominatim afin d'obtenir un dossier build, et lib, ces dossiers ont été ajouté au projet dans le dossier qui contient les controllers.

Le dossier lib contient la librairie Nominatim, et ses dépendances: Geocode.php , db.php....

Le dossier build contient les settings, settings.php permet de définir sur quelle BDD Postgresql nous allons travailler: 

	@define('CONST_Database_DSN', 'pgsql://USERNAME:PASSWORD@IP:5532/nominatim'); 

Afin d'optimiser le fonctionnement de Nominatim, lors d'une première recherche, on limite la recherche aux pays:

	Exemple de query envoyée à nominatim:
	- USA
	- U.S.A
	- States of america

	retourne: United States of America

	- Michigan
	- California
	- New York

	retourneront:
	- NULL

Ce retour NULL est une obtenu en precisant la featuretype dans le script SenderNominatim.php, on peut y sécifier: country, city,state, afin d'optimiser les temps de réponses.
Ainsi la recherche par pays se base uniquement sur les pays, nominatim permet d'uniformiser l'écriture.

L'utilisateur peut s'il le souhaite rechercher dans toute la base (ce qui impactera le temps de réponse), en cliquant sur le bouton "improve" une fois le premier passage Nominatim effectué.


	Exemple de query envoyé à nominatim:
	- USA
	- U.S.A
	- States of america

	retourne: United States of America

	- Michigan
	- California
	- New York

	retourneront:
	-  United States of America

Ainsi la recherche par pays se base sur les pays, état,région,département,ville, Nominatim permet d'uniformiser l'écriture du pays.



# Front-end:

**Librairie utilisé:**

	CSS:
	- Semantic-UI

	JS:
	- Jquery
	- Print js
	- Intégration du widget ISTEX Angular
	- DataTables JS
	- Googlechart JS
	- Leaflet JS

	Moteur de template:
	- TWIG


**Organisation du code:**

	Frontend_Istex_usage
		--css :contient la librairie Semantic UI ainsi que le css et les dépendances nécessaires au widget Istex
		--img : contient les images
		--js : contient les librairies utilisées ainsi que le code  
		--leaflet : contient les fichiers relatif à l'utilisation de leaflet(js,img,css)
		--src :contient les sources du frontend
			--index.php : contient la routes vers la vue twig
			--istex/frontend/templates contient les templates twig
		--vendor: contient les dépendances slim nécessaires au routage



**Les différentes templates:**

	- Accueil.html.twig:
		Template d'accueil, Les principaux éléments nécessaires y sont définis, les scripts js et css, la navbar, ainsi que les grilles.

	- Country_list.html.twig:
		Template affichant le tableau de résultats des pays

	- authors_list.html.twig:
		Template affichant le tableau de résultats des auteurs

	- laboratory_list.html.twig:
		Template affichant le tableau de résultats des laboratoires

	- modal_bubble_authors.html.twig:
		Template affichant le bubblechart, dans un modal, des auteurs

	- modal_bubble_laboratorys.html.twig:
		Template affichant le bubblechart, dans un modal, des laboratoires

	- modal_leaflet_country.html.twig:
		Template affichant la carte leaflet , dans un modal, des pays

	- modal_authors_table.html.twig:
		Template affichant un tableau détaillé des publications pour chaque auteurs avec id et title de la publication dans un modal

	- modal_laboratorys_table.html.twig:
		Template affichant un tableau détaillé des publications pour chaque laboratoire avec id et title de la publication dans un modal

	- widget_istex.html.twig:
		Inclusion du widget Istex avec des options définies

**Fonctionnement:**
	
	Trois scripts JS principaux:
	- laboratorys.js
	- authors.js
	- country.js

	Chaque script fait un appel ajax vers le backend afin de récupérer des données JSON qui seront ensuite traitées.
	Les appels Ajax se font dans cet ordre, il sont synchrones:
	1)laboratorys
	2)authors
	3)countrys

	Pour chaque script, les données sont traitées et affichées sur Googlechart pour les auteurs et les laboratoires, Leaflet est utilisé pour afficher les pays sur une carte du monde.

	Le widget Istex a été intégré afin de pouvoir choisir différentes facets de recherche,les valeurs de chaque champs de ce widget sont capturées.

	Dès qu'un changement est détecté, on envoie une requête vers le backend avec les paramètres choisi. 


# Diagrammes de séquences:

Diagramme de séquences générale:

![Alt text](/Img_doc/Istex_usage.png?raw=true)

Diagramme de séquences du Backend:

![Alt text](/Img_doc/Istex_usage_Backend.png?raw=true)

Diagramme de séquences du Frontend:

![Alt text](/Img_doc/Istex_usage_Frontend.png?raw=true)






# Déployer carto_istex avec Docker 

Pré-requis : avoir Docker et docker-compose d'installé et de paramétré sur sa machine, veuillez pour cela vous référer à la [documentation officielle](https://docs.docker.com/engine/installation/linux/ubuntu/#os-requirements)
	
	


Récupérez ensuite le dépôt :

```
git clone https://github.com/arnouldpy/carto_istex/
cd carto_istex/
```


La version de nominatim dans ce docker est la V3.0.0.Si vous souhaiter utiliser une version plus recente, suivez la procédure de mise a niveau décrite dans le README du dossier NOMINATIM_DOCKER

Par défaut, l'application carto-istex est mappé sur le port 8080 , si vous souhaitez modifié ce port, vous devez editer le fichier docker-compose.yml

Prensez aussi à choisir un mot de passe dans le Dockerfile présent dans le dossier Nominatim_Docker ainsi que dans le dossier Cartoistex_Docker  ENV POSTGRESQL_PASSWORD MOTDEPASSE

Vous pouvez installer tout le projet:
	- Docker de memcached
	- Docker de Carto_istex
	- Docker de Nominatim 3.0.0
	
en effectuant la commande 
```
docker-compose up
```
qui va se charger de construire les différentes images et les liers entre elles.

Une fois la generation effectué, il ne vous reste plus qu'a executer le script d'initialisation de votre choix de la base dans le container carto_istex_nominatim_img.
Pour cela executer la commande:
```
docker exec -it carto_istex_nominatim /bin/bash
Une fois dans le docker executer le script d'initialisation de votre choix  (voir ci dessous)
```

Script d'initialisation disponible:

	- install_small.sh (Script initialisant la base nominatim avec les données de Monaco, dedié au tests).
	- install_planet.sh (Script initialisant la base nominatim avec les données de la planete entière, dedié a la production,ATTENTION: le delais d'indexation peut prendre plusieurs jour, selon votre machine).
	- install_custom.sh (Script initialisant la base nominatim qui prends une URL d'un fichier de données osm.pbf en paramètre)

Vous pouvez à partir de cet instant ouvrir votre navigateur Web et accéder à l'application sur l'URL suivante :
[http://127.0.0.1:8080](http://127.0.0.1:8080)

Toutefois si vous souhaitez lancer carto_istex sans le docker nominatim suivez cette procédure:

Récupérez  le dépôt et construisez l'image docker qui sera nomée carto_istex :
```
git clone https://github.com/arnouldpy/carto_istex/
cd carto_istex/Docker/
docker build -t carto_istex_img .
```
Vous pouvez ensuite exécuter l'image en créant un conteneur à partir de cette dernière :
```
docker run -it --name carto_istex -p 127.0.0.1:8080:80 carto_istex_img
```

Pour stopper votre conteneur, il suffit ensuite de taper CTRL+C et si vous êtes en mode détaché de taper ``docker stop carto_istex`` et pour le supprimer ``docker rm carto_istex``

TODO : préparamétrer un serveur Nominatim via un conteneur docker.
ATTENTION: Pensez à configurer vos settings de Nominatim dans Backend_Istex_usage/src/istex/backend/controller/build/settings/settings.php . 
