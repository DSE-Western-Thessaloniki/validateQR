FROM ubuntu:22.04

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev dnsutils \
    libboost-program-options1.74.0 libmagick++-6.q16-8 libboost-program-options-dev libmagick++-dev cmake gcc build-essential libqrencode-dev libqrencode4 \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN mkdir /stage1
WORKDIR /stage1

RUN curl -L -O https://github.com/qpdf/qpdf/releases/download/v11.4.0/qpdf-11.4.0.tar.gz
RUN tar xvfz qpdf-11.4.0.tar.gz && mkdir qpdf-11.4.0/build
WORKDIR /stage1/qpdf-11.4.0/build
RUN cmake -DCMAKE_BUILD_TYPE=Release -DCMAKE_INSTALL_PREFIX:PATH=/usr .. && make -j$((`nproc`+1)) install

WORKDIR /stage1
RUN git clone -b 'v0.3.0' --depth 1 https://github.com/DSE-West-Thessaloniki/qpdfImageEmbed.git
WORKDIR  /stage1/qpdfImageEmbed/

RUN mkdir build
WORKDIR  /stage1/qpdfImageEmbed/build/
RUN . /etc/profile
RUN cmake -DCMAKE_BUILD_TYPE=Release .. && make -j$((`nproc`+1)) install

FROM ubuntu:22.04

LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG NODE_MAJOR=18
ARG POSTGRES_VERSION=15

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev dnsutils \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y php8.2-cli php8.2-dev \
    php8.2-pgsql php8.2-sqlite3 php8.2-gd php8.2-imagick \
    php8.2-curl \
    php8.2-imap php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-zip php8.2-bcmath php8.2-soap \
    php8.2-intl php8.2-readline \
    php8.2-ldap \
    php8.2-msgpack php8.2-igbinary php8.2-redis php8.2-swoole \
    php8.2-memcached php8.2-pcov php8.2-xdebug \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /etc/apt/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y yarn \
    && apt-get install -y mysql-client \
    && apt-get install -y postgresql-client-$POSTGRES_VERSION \
    && apt-get install -y libmagick++-6.q16-8 libboost-program-options1.74.0 libqrencode4 \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.2

RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY --from=0 /usr/lib/x86_64-linux-gnu/libqpdf* /usr/lib/x86_64-linux-gnu/
COPY --from=0 /usr/bin/qpdf /usr/bin/fix-qdf /usr/bin/zlib-flate /usr/bin/
COPY --from=0 /usr/local/bin/qpdfImageEmbed /usr/bin/
COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.2/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
