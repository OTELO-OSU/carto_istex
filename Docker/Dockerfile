FROM ubuntu:latest

ADD 000-default.conf /000-default.conf

ADD startup.sh /startup.sh

ADD memcached.conf /memcached.conf

RUN mv memcached.conf /etc/memcached.conf

RUN apt-get update && apt-get install -y apache2 php php-memcached git libapache2-mod-php php-mysql php-cli php-curl php-pear memcached  python-pip libmemcached-dev python-dev zlib1g-dev 

RUN git clone https://github.com/arnouldpy/carto_istex.git

RUN mv carto_istex /var/www/html/Projet_carto_istex

RUN pear upgrade

RUN pear install DB

RUN pip install pylibmc

RUN a2enmod rewrite

RUN chmod +x startup.sh

RUN  mv  000-default.conf /etc/apache2/sites-enabled/000-default.conf 











