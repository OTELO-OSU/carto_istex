Projet Cartographie Istex usage OTELo

Presentation et objectifs

Identifier les publications suivant plusieurs thésaurus sur les terres rares.

Cartographie de différentes informations: 
			
	-Publiant et domiciliation des auteurs.
			
	-Localisation et type de gisements étudiés ou exploités de Terres Rares.
 
	-Nombre de publications sur les Terres Rares.

	-Disciplines, permettant de mettre en évidence la multidisciplinarité ou non autour des Terres Rares.

	-Institutions ou entreprises s'interressant aux Terres Rares.

But

Ce projet permettra de compléter le travail bibliographique des chercheurs, doctorants et post-doctorants impliqués dans le projet sur les terres rares du sol à l'atmosphère, et des éléments de réponses complémentaires sur:

La connaissance des processus géologiques intervenant dans l'enrichissement des terres rares,
l’impacts des terres rares sur l'écosphère terrestre et aquatique.



Language et outils utilisé:
PHP
Memcached afin de gere la mise en cache.
Pour que la mise en cache fonctionne, il faut que l'extension memcached soit activé ainsi qu'un serveur memcached soit installé sur la machine hote.

Configuration de Memcached:
Passer la taille d'un item a maximum 12MO. Et augmenter le nombre de thread, selon la machine.


Dépendances: 
	-Apache2

	-PHP 5.6:
		Librairie php: libmemcached (ne pas oublier d'activer l'extension dans la configuration php)

	-Memcached: Fichier à Modifier /etc/memcached.conf
	Il suffit de modifier les valeurs:

	Valeur minimum recommandé:
	Allocation de 2 Go a memcached:
	-m 2048
	Allocation de 12MO maximum par item
	-I 12M
	Lancement de memcached sur 20 threads
	-t 20


	-Python:

		-PIP

		-Installation de pylibmc (librairie python memcached)

	Afin que les script python se lance convenablement il faut s'assurer que www-data ai les droits pour executer les scripts.


Détails des différentes classes et fonctions PHP:

-RequestController: cette classe va permettre d'effectuer les requetes vers l'api Istex et recuperer les données necessaire au tri.
Elle est compsé de plusieurs fonctions:

	-CurlRequest() : Prends en parametre l'url aisni que les options de type CURL, elle retourne les données de l'api Istex au format JSON.

	-Request_alldoc_querypagebypage(): Elle prends en parametre la query ( les termes de recherche de l'utilisateur),Cette fonction verifie si une reponse a laquery est présente en cache, puis si aucun cache n'est présent, elle interroge la base IStex. Les affiliations sont en suite parser et comparer a un dictionnaire de mots. Cette fonction retourne un tableau avec tous les documents qui possede une affiliations correcte.

	

-LaboratoryController:Cette classe permet de rassembler les laboratoires entre eux et ainsi les compter.

-AuthorController:Cette classe permet de rassembler les auteurs entre eux et ainsi les compter.



Détails du script python Requestprocessing (permettant un tri des affilations en multiprocessing)


	-Main(): Fonction principale, découpage en tableau du json recu, afin de lancées les différents tableaux dans un process chacun.

	-split(): permet de découper un tableau python en plusieurs petit tableau d'une taille donnée.

	-Processing(): Fonction de tri et de curation des affiliations, lancé en parralèle dans chaque process.

	-Match_result_for_laboratory():Elle prends en parametre un tableau qui est en fait les différentes partie de l'affilation.Elle compare avec le dictionnaire puis retourne le resultat qui a matché, sinon elle ne retourne rien.

	-Search_for_labo():

	-Search_for_university():

	-Search_for_university_labo_and_inst():

	-Search_for_university_labo():

	-Match_result_for_university():Elle prends en parametre un tableau qui est en fait les différentes partie de l'affilation.Elle compare avec le dictionnaire puis retourne le resultat qui a matché, sinon elle ne retourne rien.

Les deux fonctions ci dessus permettent de s'assurer de la validité des affiliations.



Détails du script Multiquery qui permet d'interroger le serveur Nominatim en local(Sans passer par une surcouche http comme le propose l'API de base):

-split(): permet de découper un tableau python en plusieurs petit tableau d'une taille donnée.

-Main(): Fonction principale, découpage en tableau du json recu, afin de lancées les différents tableaux dans un process chacun.

-Processing(): Fonction d'interrogation de nominatim faisant appel à un script php afin d'interroger la librairie PHP de Nominatim, lancé en parralèle dans chaque process.





