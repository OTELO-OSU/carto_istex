FROM debian:latest

ADD 000-default.conf /000-default.conf

ADD startup.sh /startup.sh

ADD memcached.conf /memcached.conf

RUN mv memcached.conf /etc/memcached.conf\
&& apt-get update && apt-get install -y php5 php5-memcached git libapache2-mod-php5 php5-mysql php5-pgsql php5-cli php5-curl php-pear memcached  python-pip libmemcached-dev python-dev zlib1g-dev \
&& apt-get clean \
&& git clone https://github.com/arnouldpy/carto_istex.git\
&& mv carto_istex /var/www/html/Projet_carto_istex\
&& pear upgrade\
&& pear install -f DB\
&& pip install pylibmc\
&& a2enmod rewrite\
&& echo ServerName localhost >> /etc/apache2/apache2.conf\
&& chmod +x startup.sh\
&&  mv  000-default.conf /etc/apache2/sites-enabled/000-default.conf \

ENTRYPOINT ./startup.sh && /bin/bash









