
# Docker Nominatim


La version de Nominatim dans cette image est la version 3.0.0,
Les mise à jour de la base de données OSM sont effectué toutes les 24 heures.

Si vous souhaitez mettre a jour la version de nominatim, vous devez changer de docker ou adapté celui-ci.

Pour un fonctionnement avec carto_istex il vous sera necessaire de recompiler la nouvelle version et de remplacer les dossiers lib et settings présent dans carto_istex/Backend_Istex_usage/src/istex/backend/controller/ par ceux générer, n'oubliez pas de reconfigurer vos settings dans settings/settings.php


# IMPORTANT : Librairie Geocode



