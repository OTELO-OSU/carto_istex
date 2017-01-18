#!/bin/bash
### Set Language
TEXTDOMAIN=virtualhost
### Set default parameters 
action=$1
domain="localhost"
rootDir=$2
owner=$(who am i | awk '{print $1}')
email='webmaster@localhost'
sitesEnable='/etc/apache2/sites-enabled/'
sitesAvailable='/etc/apache2/sites-available/'
userDir='/var/www/html'
sitesAvailabledomain=$sitesAvailable$domain.conf
rootDir=$userDir$rootDir

if [ "$action" == 'create' ]
	then
		### create virtual host rules file
		if ! echo "
		<VirtualHost *:80>
			ServerAdmin $email
			#ServerName $domain
			#ServerAlias $domain
			DocumentRoot $rootDir
			<Directory />
				AllowOverride All
			</Directory>
			<Directory $rootDir>
				Options Indexes FollowSymLinks MultiViews
				AllowOverride all
				Require all granted
			</Directory>
			ErrorLog /var/log/apache2/error.log
			LogLevel error
			CustomLog /var/log/apache2/access.log combined
		</VirtualHost>" > $sitesAvailabledomain
		then
			echo -e $"There is an ERROR creating $domain file"
			exit;
		else
			echo -e $"\nNew Virtual Host Created\n"
		fi

		
fi
