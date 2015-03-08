# mock-server
Mock Server for http request.

# quick start
    cd yourproject/
    composer install
    cd node/
    npm install qs
    mkdir mock
    ln -s /usr/local/node/bin/node /usr/bin/node

# open phpcas
    cd yourproject/
    echo '[common]
cas_port = 443
cas_version = 2.0
cas_context = 
cas_host = cas.your-server.com' > application/config/phpcas.ini