# mock-server
Mock Server for http request.

# quick start
    cd yourproject/
    composer install
    cd node/
    npm install qs
    npm install mysql
    mkdir mock
    ln -s /usr/local/node/bin/node /usr/bin/node

# open phpcas
    cd yourproject/
    echo '[common]
cas_port = 443
cas_version = 2.0
cas_context = 
cas_host = cas.your-server.com' > application/config/phpcas.ini

# mysql database

    DROP TABLE IF EXISTS `mock`;
    CREATE TABLE `mock` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `uri_id` int(11) unsigned NOT NULL,
      `request_query` varchar(2000) NOT NULL,
      `request_post` varchar(2000) NOT NULL,
      `response_status_code` smallint(4) unsigned NOT NULL DEFAULT '200',
      `response_header` varchar(2000) NOT NULL,
      `response_body` varchar(2000) NOT NULL,
      `timeout` int(11) unsigned NOT NULL DEFAULT '0',
      `user` varchar(50) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `stat`;
    CREATE TABLE `stat` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `uri` varchar(255) NOT NULL,
      `ip` char(16) NOT NULL,
      `time` int(10) unsigned NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

    DROP TABLE IF EXISTS `uri`;
    CREATE TABLE `uri` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `uri` varchar(2000) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
