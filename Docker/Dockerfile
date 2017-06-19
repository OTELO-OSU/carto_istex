FROM debian:jessie

RUN apt-get update && \
    apt-get install -y php5 php5-memcached git libapache2-mod-php5 php5-mysql php5-pgsql \
                       php5-cli php5-curl php-pear memcached  python-pip libmemcached-dev python-dev zlib1g-dev && \
    apt-get clean

RUN pear upgrade && \
    pear install -f DB && \
    pip install pylibmc && \
    a2enmod rewrite && \
    echo ServerName localhost >> /etc/apache2/apache2.conf

RUN git clone https://github.com/arnouldpy/carto_istex.git /var/www/html/Projet_carto_istex

COPY memcached.conf /etc/memcached.conf
COPY 000-default.conf /etc/apache2/sites-enabled/000-default.conf
COPY startup.sh /startup.sh

ENTRYPOINT /startup.sh
