# Hustoj basic file system
useradd -m 136 administation
mkdir -p /var/log/hustoj/
mv /trunk/ /home/judge/src/
chmod -R 700 /home/judge/src/web/include/
chown -R www-data:www-data /home/judge/src/web

# Adjust system configuration
cd /home/judge/src/install/default.conf  /etc/nginx/sites-available/default
