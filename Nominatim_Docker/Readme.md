
# Docker Nominatim


La version de Nominatim dans cette image est la version 3.0.0,
Les mises à jours de la base de données OSM sont effectuées toutes les 24 heures.

Si vous souhaitez mettre à jour la version de nominatim, vous devez changer de docker ou adapté celui-ci.

Pour un fonctionnement avec carto_istex il vous sera necessaire de recompiler la nouvelle version et de remplacer les dossiers lib et settings présent dans le projet carto_istex a ce chemin:carto_istex/Backend_Istex_usage/src/istex/backend/controller/ par ceux générer, n'oubliez pas de reconfigurer vos settings dans settings/settings.php.

La recompilation est necessaire car carto_istex n'utilise pas l'API fournit par Nominatim.
en effet comme expliqué dans le readme du projet, dans un soucis de temps de traitement la couche API à été retiré et l'interrogation se fait directement au niveau de la base postgres, ce qui permet de creer notre propre JSON avec seulement les clés qui nous interesse.


# IMPORTANT : Librairie Geocode

La librairie Geocode permet d'effectuer la recherche d'un mot clé dans la base, dans le projet carto_istex, il existe une version modifié qui permet de recherche uniquement au niveau pays (comme expliquer dans la documentation).
Lors d'une mise à jour de la version nominatim , il est necessaire de recréer cette version modifié en suivant les instructions suivantes:



Faites une copie de Geocode.php et renommer la copie en GeocodeImproved.php


Maintenant, modifiez cette partie du fichier Geocode.php:   

    $aViewBoxPlaceIDs = chksql(                         
         $this->oDB->getAll($sSQL),		
          "Could not get places for search terms."		
    );

en 

     $aViewBoxPlaceIDs = [];
                              
Voila , la librairie est modifie pour ne prendre que les pays de base.







Il vous faudra ensuite regenerer les deux images docker (carto_istex_img et carto_istex_nominatim_img).




