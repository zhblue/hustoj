#!/bin/bash
# -----------------------------------------
# Linux nofile 优化脚本
# 自动检测并提升文件描述符限制到合理范围
# -----------------------------------------

TARGET_LIMIT=65535
LIMITS_CONF="/etc/security/limits.d/99-nofile.conf"
SYSTEMD_CONF_DIR="/etc/systemd/system.conf.d"
SYSTEMD_CONF_FILE="$SYSTEMD_CONF_DIR/99-nofile.conf"

echo "🔍 检查当前 nofile 限制..."
CURRENT_LIMIT=$(ulimit -n)
echo "当前 nofile: $CURRENT_LIMIT"

if [ "$CURRENT_LIMIT" -lt "$TARGET_LIMIT" ]; then
    echo "⚙️  提升系统 nofile 限制到 $TARGET_LIMIT"

    # 1️⃣ 更新 limits 配置
    echo "写入 $LIMITS_CONF ..."
    sudo mkdir -p "$(dirname "$LIMITS_CONF")"
    cat <<EOF | sudo tee "$LIMITS_CONF" >/dev/null
* soft nofile $TARGET_LIMIT
* hard nofile $TARGET_LIMIT
root soft nofile $TARGET_LIMIT
root hard nofile $TARGET_LIMIT
EOF

    # 2️⃣ 更新 systemd 全局配置
    echo "写入 $SYSTEMD_CONF_FILE ..."
    sudo mkdir -p "$SYSTEMD_CONF_DIR"
    cat <<EOF | sudo tee "$SYSTEMD_CONF_FILE" >/dev/null
[Manager]
DefaultLimitNOFILE=$TARGET_LIMIT
EOF

    # 3️⃣ 重载 systemd
    echo "🔁 重新加载 systemd 配置..."
    sudo systemctl daemon-reexec
    sudo systemctl daemon-reload

    echo "✅ 已设置 nofile = $TARGET_LIMIT"
    echo "⚠️  注意：当前 shell 仍使用旧值，请执行以下命令生效："
    echo "    ulimit -n $TARGET_LIMIT"
else
    echo "✅ 当前 nofile ($CURRENT_LIMIT) 已足够，无需修改。"
fi

# 显示结果
echo
echo "🧾 当前系统 nofile 限制："
ulimit -n
echo "文件 /etc/security/limits.d/99-nofile.conf:"
cat "$LIMITS_CONF"
