#Projet Cartographie Istex usage OTELo

**Presentation et objectifs**

Identifier les publications suivant plusieurs thésaurus sur les terres rares.

Cartographie de différentes informations: 
			
	- Publiant et domiciliation des auteurs.
			
	- Localisation et type de gisements étudiés ou exploités de Terres Rares.
 
	- Nombre de publications sur les Terres Rares.

	- Disciplines, permettant de mettre en évidence la multidisciplinarité ou non autour des Terres Rares.

	- Institutions ou entreprises s'interressant aux Terres Rares.

**But**

Ce projet permettra de compléter le travail bibliographique des chercheurs, doctorants et post-doctorants impliqués dans le projet sur les terres rares du sol à l'atmosphère, et des éléments de réponses complémentaires sur:

La connaissance des processus géologiques intervenant dans l'enrichissement des terres rares,
l’impacts des terres rares sur l'écosphère terrestre et aquatique.


**Introduction**

L’application Carto Istex permet la réalisation de cartographie d'informations à partir du réservoir de publication ISTEX suivant leur provenance (Pays, Laboratoire) et leurs auteurs.
Elle est composé d’un backend codé en PHP avec le micro-framework SLIM ainsi que d’un frontend codé en HTML,CSS ,JavaScript et de la librairie Jquery.


**Principe de fonctionnement :**

Un utilisateur effectue une recherche (query) dans le champs prévu à cet effet. Champs issu de l'intégration du widget Istex-SNU.
Différents appels POST AJAX avec en paramètre la query vont être envoyé vers le backend.
Une réponse au format JSON va être retourné contenant les informations à traiter et à afficher.
La réponse est traitée par les  scripts JavaScript :, getcountry pour les pays, getlaboratory pour les laboratoires ainsi que getauthor pour les auteurs.
L’utilisateur peut ensuite consulter les résultats sous forme de tableau dynamique et paginé, ou sous forme de carte exportable en format PDF et/ou PNG.



**Aspect de générale de l’application :**

Pour l’aspect CSS le framework Semantic UI a été choisi pour sa simplicité d’utilisation et sa bonne documentation. Il permet de réaliser des interfaces graphiques responsive rapidement. 
Pour afficher une carte des pays, la librairie Leaflet a été choisie, pour sa simplicité d’utilisation ainsi que sa légèreté. L’utilisateur à la possibilité d’exporter la carte générée au format PDF ou même de l’imprimer.
Afin de réaliser des Bubblechart (Graphique à bulles) la librairie GoogleChart a été utilisée. L’utilisateur peut enregistrer le graphique généré au format PNG.
Le plugin Jquery Datatable à été utilisé pour rendre dynamique les tableaux, ainsi on peut aisément effectuer une recherche dans les tableaux de résultat ou même les trier par ordre alphabétique ou par nombre. Le nombre d’affiliations erronées , (publication possédant une affiliation qui ne peut être traité correctement) est affiché pour chaque catégories, le nombre diffère selon la catégorie, en effet une publications peut avoir un auteur mais une mauvaise affiliations.
Le Widget de recherche ISTEX à été intégrer à l’application.



![Alt text](/Img_doc/Accueil.png?raw=true)







#Language et outils utilisé:

PHP
Memcached afin de gere la mise en cache.
Pour que la mise en cache fonctionne, il faut que l'extension memcached soit activé ainsi qu'un serveur memcached soit installé sur la machine hote.

**Configuration de Memcached:**

Passer la taille d'un item a maximum 24MO. 


**Dépendances:**

	- Apache2

	- PHP 5.6:
		Librairie php: libmemcached (ne pas oublier d'activer l'extension dans la configuration php)
		Php Curl afin d'effectuer les requetes sur l'API ISTEX
		Php pgsql pour acceder a la base pgsql de nominatim
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

	Afin que les script python se lance convenablement il faut s'assurer que www-data ai les droits pour executer les scripts.


#Back-end:

**Organisation du code:**

	Backend_Istex_usage
		--src
			--istex 
				--backend/controller contient les controllers ainsi que les dépendances nominatim  
			--index.php : fichier de routes
			--Multiquery.py
			--Requestprocessing.py 
		--vendor : contient les dependances slim necessaires au routage


**Détails des différentes classes et fonctions PHP:**

-**RequestController**: 
Cette classe va permettre d'effectuer les requetes vers l'api Istex et recuperer les données necessaire au tri.
Elle est compsé de plusieurs fonctions:

	- CurlRequest() : Prends en parametre l'url aisni que les options de type CURL, elle retourne les données de l'api Istex au format JSON.

	- Request_alldoc_querypagebypage(): Elle prends en parametre la query ( les termes de recherche de l'utilisateur),Cette fonction verifie si une reponse a laquery est présente en cache, puis si aucun cache n'est présent, elle interroge la base IStex. Les affiliations sont en suite parser et comparer a un dictionnaire de mots. Cette fonction retourne un tableau avec tous les documents qui possede une affiliations correcte.

	

-**LaboratoryController**:
Cette classe permet de rassembler les laboratoires entre eux et ainsi les compter.

-**AuthorController**:
Cette classe permet de rassembler les auteurs entre eux et ainsi les compter.



**Détails du script python Requestprocessing (permettant un tri des affilations en multiprocessing)**


	- Main(): Fonction principale, découpage en tableau du json recu, afin de lancées les différents tableaux dans un process chacun.

	- split(): permet de découper un tableau python en plusieurs petit tableau d'une taille donnée.

	- Processing(): Fonction de tri et de curation des affiliations, lancé en parralèle dans chaque process.

	- Match_result_for_laboratory():Elle prends en parametre un tableau qui est en fait les différentes partie de l'affilation.Elle compare avec le dictionnaire puis retourne le resultat qui a matché, sinon elle ne retourne rien.

	- Search_for_labo():

	- Search_for_university():

	- Search_for_university_labo_and_inst():

	- Search_for_university_labo():

	- Match_result_for_university():Elle prends en parametre un tableau qui est en fait les différentes partie de l'affilation.Elle compare avec le dictionnaire puis retourne le resultat qui a matché, sinon elle ne retourne rien.

Les deux fonctions ci dessus permettent de s'assurer de la validité des affiliations.

**Détails du script Multiquery qui permet d'interroger le serveur Nominatim en local(Sans passer par une surcouche http comme le propose l'API de base):**

- split(): permet de découper un tableau python en plusieurs petit tableau d'une taille donnée.

- Main(): Fonction principale, découpage en tableau du json recu, afin de lancées les différents tableaux dans un process chacun.

- Processing(): Fonction d'interrogation de nominatim faisant appel à un script php afin d'interroger la librairie PHP de Nominatim, lancé en parralèle dans chaque process.


#Details du traitement des affiliations:

**Présentation d’une affiliation :**

"Laboratoire de Géologie des bassins sédimentaires, Université Paris VI, 4 place Jussieu, Paris, France"

Certaines affiliations contiennent des point-virgules comme séparateur, on transforme donc ceux ci en virgules.Le même traitement est effectué pour les tirets.
L’affiliation est découpé en lots, chaque lot, ces lots sont « nettoyées » des caractères spéciaux et accents afin d’effectuer une comparaison optimal.

Le couple laboratoire, institution est obtenus avec différents traitement selon la forme d’écriture de l’affiliation.
Pour cela on utilise deux dictionnaires de données(Institution-All,Labo-All,voir le diagramme), un pour les laboratoires ainsi qu’un pour les institutions.

Cas général :

En effet une affiliation peut avoir un couple laboratoire, institution, si les données contenue dans l’affiliation match avec celles contenue dans le dictionnaire de données , on obtient bien un couple laboratoire,institution.

Cas particuliers : 

Si une affiliation possèdent seulement un laboratoire une fois celle ci comparé avec le dictionnaire de données, pour les cas contenus dans le dictionnaire Labo-service, on affichera le laboratoire ainsi que dans le champs institution un nom considéré comme une institution.

**Exemple :**

Dans l’affiliation lors du passage avec les dictionnaires général, on obtient:
-LABORATORY FOR GEOCHEMICAL RESEARCH	
N’ayant pas d’institution, une recherche d’un autre laboratoire pouvant être considéré comme une institution va être lancé, on obtient :
-HUNGARIAN ACADEMY OF SCIENCES
On va considérer que le terme academy est une institution.
On vérifie que le laboratoire n’est pas égal à l’institution.

Ce qui donnera :

Laboratoire :LABORATORY FOR GEOCHEMICAL RESEARCH	
Insitution :HUNGARIAN ACADEMY OF SCIENCES


Si une affiliation possèdent seulement une institution,on recherchera avec le dictionnaire (Institution-Major,dictionnaire d’institution confirmée)dans le cas ou un résultat ne match pas,on déclare vide le champs institution,sinon on le recherche dans le dictionnaire (Institution-Labo ,dictionnaires de nom pouvant être des laboratoires), si il y a un match alors on affiche l’institution trouvé avec le dictionnaire générale ainsi que la deuxième institution si elle n’est pas égal à la première.Si le résultat ne match pas, alors on recherche dans le dictionnaire (Institution-institution, dictionnaires d’institution étant des laboratoires) et s’il y a un match on affiche deux fois la même institution.

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







#Front-end:

**Librairie utilisé:**

	CSS:
	-Semantic-UI

	JS:
	- Jquery
	- Print js
	- Integration du widget ISTEX Angular
	- DataTables JS
	- Googlechart JS
	- Leaflet JS

	Moteur de template:
	- TWIG


**Organisation du code:**

	Frontend_Istex_usage
		--css :contient la librairie Semantic UI ainsi que le css produit et les dependances necessaires au widget Istex
		--img : contient les images
		--js : contient les librairies utilisés ainsi que le code produit 
		--leaflet : contient les fichiers relatif à l'utilisation de leaflet(js,img,css)
		--src :contient les sources du frontend
			--index.php : contient la routes vers la vue twig
			--istex/frontend/templates contient les templates twig
		--vendor: contient les dependances slim necessaires au routage



**Les différentes templates:**

	- Accueil.html.twig:
		Template d'accueil, Les principaux éléments neccessaires y sont defini, les script js et css, la navbar, ainsi que les grilles.



#Diagrammes de séquences:

![Alt text](/Img_doc/Istex_usage.png?raw=true)

![Alt text](/Img_doc/Istex_usage_Backend.png?raw=true)

![Alt text](/Img_doc/Istex_usage_Frontend.png?raw=true)





#Docker 

**Un conteneur Docker est disponible:**

Pour l'executer, il faut installer Docker.

**Exemple pour ubuntu:**

		- sudo apt-get install apt-transport-https ca-certificates
		- sudo apt-key adv \
               --keyserver hkp://ha.pool.sks-keyservers.net:80 \
               --recv-keys 58118E89F3A912897C070ADBF76221572C52609D
        - sudo apt-get update
        - sudo apt-get install docker-engine

**Une fois Docker installé:**
    	
    	Recuperer les fichiers contenu dans le dossier Docker :
	svn checkout https://github.com/arnouldpy/carto_istex/trunk/Docke
	executer la commande:
    	- sudo docker build .
   		Attendre la generation de l'image

**Une fois l'image generé:**

    	- sudo docker images

    Prendre l'id de l'image generer et l'ajouter à cette commande qui va creer le container à partir de l'image:

    	- sudo docker run  -i -t -p 127.0.0.1:8080:80 IDHERE ./startup.sh


    On recupere l'id du container,

    	- sudo docker ps 

    Pour relancer le container une fois celui ci créé:

   		- sudo docker start CONTAINERID

    Pour l'arreter:

    	- sudo docker stop CONTAINERID

    Pour executer des commandes:

    	- sudo docker exec 
    
