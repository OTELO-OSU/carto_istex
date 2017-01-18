FROM debian:latest

ADD modifyapache.sh /modifyapache.sh

ADD startup.sh /startup.sh

ADD memcached.conf /memcached.conf

RUN mv memcached.conf /etc/memcached.conf

RUN apt-get update && apt-get install -y php5 php5-memcached git libapache2-mod-php5 php5-mysql php5-cli php5-curl php-pear memcached  python-pip libmemcached-dev python-dev zlib1g-dev 

RUN git clone https://github.com/arnouldpy/carto_istex.git

RUN mv carto_istex /var/www/html/Projet_carto_istex

RUN pear upgrade

RUN pear install DB

RUN pip install pylibmc

RUN a2enmod rewrite

RUN chmod +x modifyapache.sh

RUN chmod +x startup.sh

RUN ./modifyapache.sh create /var/www/html/Projet_carto_istex

RUN rm  /etc/apache2/sites-enabled/000-default.conf 

RUN  mv  /etc/apache2/sites-enabled/localhost.conf /etc/apache2/sites-enabled/000-default.conf 











