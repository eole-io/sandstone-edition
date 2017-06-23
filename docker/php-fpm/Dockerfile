FROM phpdockerio/php71-fpm

RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.1-mysql php-zmq \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install blackfire extension
RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp \
    && mv /tmp/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
    && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > /etc/php/$(php -r "echo PHP_MAJOR_VERSION, '.', PHP_MINOR_VERSION;")/fpm/conf.d/10-blackfire.ini

WORKDIR "/var/www/html"
