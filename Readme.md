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



Détails des différentes classes et fonctions:

-RequestController: cette classe va permettre d'effectuer les requetes vers l'api Istex et recuperer les données necessaire au tri.
Elle est compsé de plusieurs fonctions:

	-CurlRequest() : Prends en parametre l'url aisni que les options de type CURL, elle retourne les données de l'api Istex au format JSON.

	-Request_alldoc_querypagebypage(): Elle prends en parametre la query ( les termes de recherche de l'utilisateur),Cette fonction verifie si une reponse a laquery est présente en cache, puis si aucun cache n'est présent, elle interroge la base IStex. Les affiliations sont en suite parser et comparer a un dictionnaire de mots. Cette fonction retourne un tableau avec tous les documents qui possede une affiliations correcte.

	-Match_result_for_laboratory():Elle prends en parametre un tableau qui est en fait les différentes partie de l'affilation.Elle compare avec le dictionnaire puis retourne le resultat qui a matché, sinon elle ne retourne rien.


	-Match_result_for_university():Elle prends en parametre un tableau qui est en fait les différentes partie de l'affilation.Elle compare avec le dictionnaire puis retourne le resultat qui a matché, sinon elle ne retourne rien.

	Les deux fonctions ci dessus permettent de s'assurer de la validité des affiliations.


-LaboratoryController:Cette classe permet de rassembler les laboratoires entre eux et ainsi les compter.






