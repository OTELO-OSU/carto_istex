FROM php:5.6.30-apache


RUN rm -f /etc/apache2/mods-available/php5.load
RUN apt-get update && \
    apt-get install -y  python-pip libmemcached-dev python-dev zlib1g-dev libapache2-mod-php5 php5-memcached php-pear php5-cli php-db php5-curl php5-pgsql php5-intl && \
    apt-get clean


RUN pear install -f DB && \
    pip install pylibmc && \
    a2enmod rewrite 

    

COPY . /var/www/html/Projet_carto_istex/


COPY ./Cartoistex_Docker/entrypoint.sh entrypoint.sh
RUN  chmod +x entrypoint.sh 


COPY ./Cartoistex_Docker/000-default.conf /etc/apache2/sites-enabled/000-default.conf
ENTRYPOINT ["/var/www/html/entrypoint.sh"]
