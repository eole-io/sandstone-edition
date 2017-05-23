FROM shaula/rpi-php7-fpm

# install zmq and php-zmq
RUN apt-get update \
 && apt-get install build-essential libtool autoconf uuid-dev pkg-config git libsodium-dev wget bzip2

RUN wget https://archive.org/download/zeromq_4.1.4/zeromq-4.1.4.tar.gz \
 && tar -xvzf zeromq-4.1.4.tar.gz \
 && cd zeromq-4.1.4 \
 && ./configure \
 && make \
 && sudo make install \
 && sudo ldconfig \
 && cd .. \
 && rm -fr zeromq-4.1.4.tar.gz zeromq-4.1.4/

RUN git clone git://github.com/mkoppanen/php-zmq.git \
 && cd php-zmq \
 && phpize && ./configure \
 && make \
 && make install \
 && cd .. \
 && rm -fr php-zmq

RUN echo "extension=zmq.so" > /usr/local/etc/php/conf.d/docker-php-ext-zmq.ini

RUN apt-get remove git build-essential \
 && apt-get autoremove --purge \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# install PHP extensions & PECL modules with dependencies
RUN apt-get update \
 && apt-get install -y \
        libicu-dev \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN docker-php-ext-install intl \
 && docker-php-ext-install pdo_mysql mysqli

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
 && php composer-setup.php --filename=composer \
 && php -r "unlink('composer-setup.php');" \
 && mv composer /usr/local/bin/composer

WORKDIR "/var/www/html"
