
# Docker Nominatim


La version de Nominatim dans cette image est la version 3.0.0,
Les mises à jours de la base de données OSM sont effectuées toutes les 24 heures.

Si vous souhaitez mettre à jour la version de nominatim, vous devez changer de docker ou adapter celui-ci.

Pour un fonctionnement avec carto_istex il vous sera necessaire de recompiler la nouvelle version et de remplacer les dossiers lib et settings présents dans le projet carto_istex à ce chemin: carto_istex/Backend_Istex_usage/src/istex/backend/controller/ par ceux générés, n'oubliez pas de reconfigurer vos settings dans settings/settings.php.

La recompilation est necessaire car carto_istex n'utilise pas l'API fournit par Nominatim en effet dans un soucis de temps de traitement la couche API à été désactivée et l'interrogation se fait directement en sql via la base postgresql (Ce qui permet de créer notre propre JSON avec seulement les clés/valeurs qui nous interessent).


Il vous faudra ensuite regénérer les deux images docker (carto_istex_img et carto_istex_nominatim_img).
(cf documentation carto_istex)




