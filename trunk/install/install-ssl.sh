#!/bin/bash
# HUSTOJ Let's Encrypt 一键集成（幂等，可重复执行）
#
# 用法: sudo bash install-ssl.sh your.domain.com
# 前提: 域名已 A/AAAA 解析到本机，80 端口可被外网访问（ACME webroot 验证）。
#
# 做四件事:
#   1. 安装 acme.sh（已装则跳过，零 Python 依赖）
#   2. webroot 模式签发证书（复用 /home/judge/src/web，不动现有 80 vhost）
#   3. 部署证书到 /etc/nginx/ssl/hustoj/ 并生成 hustoj-ssl.conf（443 vhost，
#      内含 /upload/ 禁执行 php 的安全基线）
#   4. 注册续期: acme.sh 自装 cron 每天检查，到期自动续 + 自动部署 + reload nginx

set -e
DOMAIN=$1
[ "$(id -u)" = "0" ] || { echo "请用 root/sudo 运行"; exit 1; }
[ -n "$DOMAIN" ] || { echo "用法: sudo bash install-ssl.sh your.domain.com"; exit 1; }
grep -qE '^[A-Za-z0-9.-]+\.[A-Za-z]{2,}$' <<<"$DOMAIN" || { echo "域名格式不正确: $DOMAIN"; exit 1; }

ACME_HOME="$HOME/.acme.sh"
SSL_DIR=/etc/nginx/ssl/hustoj
CONF=/etc/nginx/conf.d/hustoj-ssl.conf
WEBROOT=/home/judge/src/web
SRC_DIR=$(cd "$(dirname "$0")" && pwd)

[ -d "$WEBROOT" ] || { echo "找不到 $WEBROOT，请先完成 HUSTOJ 安装"; exit 1; }

# ① 安装 acme.sh（幂等）
if [ ! -x "$ACME_HOME/acme.sh" ]; then
    echo "[1/4] 安装 acme.sh ..."
    tmp=$(mktemp -d)
    git clone --depth 1 https://github.com/acmesh-official/acme.sh.git "$tmp"
    cd "$tmp"
    ./acme.sh --install --home "$ACME_HOME"
    cd -
    rm -rf "$tmp"
else
    echo "[1/4] acme.sh 已安装，跳过"
fi

# ② 签发（已签发则跳过；--force 可手动重签）
if [ ! -f "$ACME_HOME/$DOMAIN/$DOMAIN.ecc.cer" ] && [ ! -f "$ACME_HOME/$DOMAIN/$DOMAIN.cer" ]; then
    echo "[2/4] ACME webroot 签发证书: $DOMAIN ..."
    mkdir -p "$WEBROOT/.well-known/acme-challenge"
    chown -R www-data:www-data "$WEBROOT/.well-known" 2>/dev/null || true
    "$ACME_HOME/acme.sh" --issue --server letsencrypt -d "$DOMAIN" -w "$WEBROOT" --keylength ec-256
else
    echo "[2/4] 证书已存在，跳过签发"
fi

# ③ 部署证书 + 生成 vhost
echo "[3/4] 部署证书到 $SSL_DIR ..."
mkdir -p "$SSL_DIR/$DOMAIN"
"$ACME_HOME/acme.sh" --install-cert -d "$DOMAIN" --ecc \
    --fullchain-path "$SSL_DIR/$DOMAIN/fullchain.cer" \
    --key-path "$SSL_DIR/$DOMAIN/domain.key" \
    --reloadcmd "nginx -t && systemctl reload nginx"

# fastcgi 后端自适应: install 脚本把部分环境改成了 127.0.0.1:9001(TCP)，
# 其余用默认 unix socket。取当前 default vhost 里生效的那行。
if grep -E '^\s*fastcgi_pass\s+127\.0\.0\.1:9001;' /etc/nginx/sites-enabled/default >/dev/null 2>&1; then
    FASTCGI='fastcgi_pass                127.0.0.1:9001;'
else
    FASTCGI='fastcgi_pass                unix:/run/php/php'"$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"'-fpm.sock;'
fi

echo "[3/4] 生成 $CONF ..."
# nginx < 1.25.1（Ubuntu 24.04 自带 1.24）不认独立的 `http2 on;`，需退回 listen 老写法。
NGX_VER=$(nginx -v 2>&1 | sed 's/.*nginx\///')
if [ "$(printf '%s\n1.25.1\n' "$NGX_VER" | sort -V | head -1)" != "1.25.1" ]; then
    HTTP2_SED='s/^    http2 on;$//; s/^    listen 443 ssl;/    listen 443 ssl http2;/; s/^    listen \[::\]:443 ssl;/    listen [::]:443 ssl http2;/'
else
    HTTP2_SED=''
fi
sed -e "s/__HUSTOJ_SSL_DOMAIN__/$DOMAIN/g" \
    -e "s|__HUSTOJ_FASTCGI_PASS__|$FASTCGI|" \
    ${HTTP2_SED:+-e "$HTTP2_SED"} \
    "$SRC_DIR/hustoj-ssl.conf" > "$CONF.tmp"
# 先把待启用配置挂到临时主配置里校验，通过后才落地，失败不影响运行中的 nginx
cat > /tmp/hustoj-ssl-test.conf <<'EOF'
worker_processes 1;
events {}
http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;
    server { listen 8081; server_name _; return 444; }
}
EOF
echo "http { include $CONF.tmp; }" >> /tmp/hustoj-ssl-test.conf
if nginx -t -c /tmp/hustoj-ssl-test.conf 2>&1; then
    mv "$CONF.tmp" "$CONF"
    nginx -t && systemctl reload nginx
    echo "[4/4] 完成。验证: curl -I https://$DOMAIN/"
else
    rm -f "$CONF.tmp" /tmp/hustoj-ssl-test.conf
    echo "nginx 配置校验失败，已回滚，未改动运行中的 nginx"; exit 1
fi
rm -f /tmp/hustoj-ssl-test.conf
