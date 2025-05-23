FROM alpine:3.20

ARG WWWGROUP
ARG WWWUSER

# Setup demo environment variables
ENV LANG=en_US.UTF-8 \
    LANGUAGE=en_US.UTF-8 \
    LC_ALL=en_US.UTF-8 \
    DISPLAY=:0.0

RUN apk update && \
    apk add \
    novnc \
    bash \
    fluxbox \
    terminus-font \
    supervisor \
    tigervnc \
    py3-pip

RUN pip3 install --break-system-packages websockify

RUN ln -s /usr/share/novnc/vnc.html /usr/share/novnc/index.html

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080

# RUN addgroup -g ${WWWGROUP} sail
# Alpine addgroup does not support gids and uids over 256000 so we improvise
RUN echo "sail:x:${WWWUSER}:${WWWGROUP}::/home/sail:/bin/sh" >> /etc/passwd && \
    echo "sail:x:${WWWGROUP}:" >> /etc/group && \
    echo "sail:x::0:99999:7:::" >> /etc/shadow

# do not log creating new user, otherwise there could be a lot of messages
#RUN adduser --disabled-password --gecos "" --uid ${WWWUSER} --ingroup sail sail
#RUN install -d -m 0755 -o sail -g sail /home/sail

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
