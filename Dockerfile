FROM php
RUN docker-php-ext-install pdo_mysql mbstring ctype iconv
CMD ["php", "-S", "0.0.0.0:8001", "-t", "public"]

