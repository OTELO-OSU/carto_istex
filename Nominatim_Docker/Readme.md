
# Docker Nominatim


La version de Nominatim dans cette image est la version 3.0.0,
Les mises à jours de la base de données OSM sont effectuées toutes les 24 heures.

Si vous souhaitez mettre à jour la version de nominatim, vous devez changer de docker ou adapté celui-ci.

Pour un fonctionnement avec carto_istex il vous sera necessaire de recompiler la nouvelle version et de remplacer les dossiers lib et settings présent dans le projet carto_istex a ce chemin:carto_istex/Backend_Istex_usage/src/istex/backend/controller/ par ceux générer, n'oubliez pas de reconfigurer vos settings dans settings/settings.php.

La recompilation est neccessaire car carto_istex n'utilise pas l'API fournit par Nominatim.
en effet comme expliqué dans le readme du projet, dans un soucis de temps de traitement la couche API à été retiré et l'interrogation se fait directement au niveau de la base postgres, ce qui permet de creer notre propre JSON avec seulement les clés qui nous interesse.


# IMPORTANT : Librairie Geocode





Il vous faudra ensuite regenerer les deux images docker (carto_istex_img et carto_istex_nominatim_img).



