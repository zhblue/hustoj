set -xe

# Hustoj basic file system
useradd -m -u 1536 judge
mkdir -p /home/judge/data
mkdir -p /var/log/hustoj/
mv /trunk/ /home/judge/src/
chmod -R 700 /home/judge/src/web/include/
chown -R www-data:www-data /home/judge/data
chown -R www-data:www-data /home/judge/src/web

PHP_VER=`apt-cache search php-fpm|grep -e '[[:digit:]]\.[[:digit:]]' -o`
if [ "$PHP_VER" = "" ] ; then PHP_VER="8.1"; fi

# Adjust system configuration
cp /home/judge/src/install/default.conf  /etc/nginx/sites-available/default
sed -i "s#127.0.0.1:9000#unix:/var/run/php/php$PHP_VER-fpm.sock#g"    /etc/nginx/sites-available/default
