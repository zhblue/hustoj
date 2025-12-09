#!/bin/bash

# 配置文件路径
PHP_FILE="../web/include/db_info.inc.php"  # 修改为实际的配置文件路径

# 检查配置文件是否存在
check_config_file() {
    if [ ! -f "$PHP_FILE" ]; then
        echo "错误: 配置文件 $PHP_FILE 不存在"
        exit 1
    fi
}

# 显示当前配置（包含注释）
show_current_config() {
    local var_name="$1"
    local current_line=$(grep -n "static[[:space:]]*\\\$$var_name" "$PHP_FILE" | head -1)
    
    if [ -n "$current_line" ]; then
        local line_num=$(echo "$current_line" | cut -d: -f1)
        local full_line=$(echo "$current_line" | cut -d: -f2-)
        
        # 分割配置部分和注释部分
        local config_part=$(echo "$full_line" | sed 's/;.*//')
        local comment_part=$(echo "$full_line" | grep -o '//.*' 2>/dev/null)
        
        echo "当前配置行号: $line_num"
        echo "当前配置: $config_part"
        
        # 提取当前值
        CURRENT_VALUE=$(echo "$config_part" | grep -oP '=\s*"\K[^"]*' 2>/dev/null)
        if [ -z "$CURRENT_VALUE" ]; then
            CURRENT_VALUE=$(echo "$config_part" | grep -oP "=\s*'\K[^']*" 2>/dev/null)
        fi
        if [ -z "$CURRENT_VALUE" ]; then
            CURRENT_VALUE=$(echo "$config_part" | grep -oP '=\s*\K[^;]*' 2>/dev/null | tr -d ' ' | tr -d ';')
        fi
        
        # 显示注释（如果存在）
        if [ -n "$comment_part" ]; then
            echo ""
            echo "注释说明: $comment_part"
        fi
    else
        echo "未找到变量 \$$var_name 的配置"
        CURRENT_VALUE=""
    fi
}

# 更新配置变量
update_config() {
    local var_name="$1"
    local new_value="$2"
    
    echo ""
    echo "正在更新 \$$var_name ..."
    
    # 判断值的类型
    if [[ "$new_value" =~ ^[0-9]+(\.[0-9]+)?$ ]]; then
        # 数字值（整数或小数）
        sed -i "s/static[[:space:]]*\\\$$var_name[[:space:]]*=[[:space:]]*[^;]*;/static \$$var_name=$new_value;/" "$PHP_FILE"
    elif [[ "$new_value" =~ ^(true|false)$ ]]; then
        # 布尔值
        sed -i "s/static[[:space:]]*\\\$$var_name[[:space:]]*=[[:space:]]*[^;]*;/static \$$var_name=$new_value;/" "$PHP_FILE"
    elif [[ "$new_value" =~ ^'.*'$ ]]; then
        # 已经是单引号包裹的字符串
        sed -i "s/static[[:space:]]*\\\$$var_name[[:space:]]*=[[:space:]]*[^;]*;/static \$$var_name=$new_value;/" "$PHP_FILE"
    elif [[ "$new_value" =~ ^\".*\"$ ]]; then
        # 已经是双引号包裹的字符串
        sed -i "s/static[[:space:]]*\\\$$var_name[[:space:]]*=[[:space:]]*[^;]*;/static \$$var_name=$new_value;/" "$PHP_FILE"
    else
        # 字符串值（默认用双引号包裹）
        sed -i "s/static[[:space:]]*\\\$$var_name[[:space:]]*=[[:space:]]*\"[^\"]*\"/static \$$var_name=\"$new_value\"/" "$PHP_FILE"
        # 如果没有双引号格式，尝试单引号格式
        if [ $? -ne 0 ]; then
            sed -i "s/static[[:space:]]*\\\$$var_name[[:space:]]*=[[:space:]]*'[^']*'/static \$$var_name='$new_value'/" "$PHP_FILE"
        fi
        # 如果既不是双引号也不是单引号，可能是布尔/数字，直接用新值替换
        if [ $? -ne 0 ]; then
            sed -i "s/static[[:space:]]*\\\$$var_name[[:space:]]*=[[:space:]]*[^;]*;/static \$$var_name=$new_value;/" "$PHP_FILE"
        fi
    fi
    
    if [ $? -eq 0 ]; then
        echo "✓ 更新成功"
    else
        echo "✗ 更新失败"
        return 1
    fi
}

# 加载当前配置到内存（支持各种格式）
load_current_config() {
    echo "正在加载配置..."
    
    # 清空之前加载的变量
    unset DB_HOST DB_NAME DB_USER DB_PASS OJ_NAME OJ_HOME OJ_ADMIN OJ_DATA
    unset OJ_BBS OJ_ONLINE OJ_LANG OJ_SIM OJ_DICT OJ_LANGMASK OJ_ACE_EDITOR
    unset OJ_AUTO_SHARE OJ_CSS OJ_SAE OJ_VCODE OJ_REG_SPEED OJ_APPENDCODE
    unset OJ_CE_PENALTY OJ_PRINTER OJ_MAIL OJ_MARK OJ_MEMCACHE OJ_MEMSERVER
    unset OJ_MEMPORT OJ_UDP OJ_UDPSERVER OJ_UDPPORT OJ_JUDGE_HUB_PATH
    unset OJ_REDIS OJ_REDISSERVER OJ_REDISPORT OJ_REDISQNAME
    unset SAE_STORAGE_ROOT OJ_CDN_URL OJ_TEMPLATE OJ_BG OJ_LOGIN_MOD
    unset OJ_REGISTER OJ_REG_NEED_CONFIRM OJ_EMAIL_CONFIRM OJ_EXPIRY_DAYS
    unset OJ_NEED_LOGIN OJ_LONG_LOGIN OJ_KEEP_TIME OJ_AUTO_SHOW_OFF
    unset OJ_RANK_LOCK_PERCENT OJ_RANK_LOCK_DELAY OJ_SHOW_METAL
    unset OJ_AINO OJ_SHOW_DIFF OJ_HIDE_RIGHT_ANSWER OJ_DL_1ST_WA_ONLY
    unset OJ_DOWNLOAD OJ_TEST_RUN OJ_MATHJAX OJ_BLOCKLY OJ_ENCODE_SUBMIT
    unset OJ_OI_1_SOLUTION_ONLY OJ_OI_MODE OJ_BENCHMARK_MODE
    unset OJ_CONTEST_RANK_FIX_HEADER OJ_NOIP_KEYWORD OJ_NOIP_HINT
    unset OJ_CONTEST_LIMIT_KEYWORD OJ_OFFLINE_ZIP_CCF_DIRNAME OJ_BEIAN
    unset OJ_RANK_HIDDEN OJ_FRIENDLY_LEVEL OJ_FREE_PRACTICE
    unset OJ_SUBMIT_COOLDOWN_TIME OJ_POISON_BOT_COUNT OJ_MARKDOWN
    unset OJ_INDEX_NEWS_TITLE OJ_DIV_FILTER OJ_LIMIT_TO_1_IP
    unset OJ_REMOTE_JUDGE OJ_NO_CONTEST_WATCHER OJ_CONTEST_TOTAL_100
    unset OJ_OLD_FASHINED OJ_AI_HTML OJ_PUBLIC_STATUS OJ_FANCY_RESULT
    unset OJ_FANCY_MP3 OJ_AI_API_URL SMTP_SERVER SMTP_PORT SMTP_USER SMTP_PASS
    
    # 使用更强大的解析方法
    while IFS= read -r line; do
        # 去除注释
        clean_line=$(echo "$line" | sed 's/\/\/.*//' | sed 's/#.*//')
        
        # 匹配 static $VAR = value; 格式
        if [[ "$clean_line" =~ static[[:space:]]+\$([A-Z_]+)[[:space:]]*=[[:space:]]*(.*)\; ]]; then
            var_name="${BASH_REMATCH[1]}"
            raw_value="${BASH_REMATCH[2]}"
            
            # 去除值末尾可能的空白
            raw_value="${raw_value%"${raw_value##*[![:space:]]}"}"
            
            # 解析值
            if [[ "$raw_value" =~ ^\"([^\"]*)\" ]]; then
                # 双引号字符串
                var_value="${BASH_REMATCH[1]}"
            elif [[ "$raw_value" =~ ^\'([^\']*)\' ]]; then
                # 单引号字符串
                var_value="${BASH_REMATCH[1]}"
            elif [[ "$raw_value" =~ ^(true|false)$ ]]; then
                # 布尔值
                var_value="$raw_value"
            elif [[ "$raw_value" =~ ^[0-9]+(\.[0-9]+)?$ ]]; then
                # 数字值
                var_value="$raw_value"
            elif [[ "$raw_value" =~ ^[A-Za-z0-9_/.@:-]+$ ]]; then
                # 可能是不加引号的字符串或路径
                var_value="$raw_value"
            else
                # 其他复杂情况，保持原样
                var_value="$raw_value"
            fi
            
            # 将变量赋值到内存
            eval "$var_name=\"$var_value\""
        fi
    done < "$PHP_FILE"
    
    echo "配置加载完成"
}

# 编辑变量
edit_variable() {
    local var_name="$1"
    
    # 显示当前配置（包含注释）
    show_current_config "$var_name"
    
    if [ -z "$CURRENT_VALUE" ]; then
        echo "无法读取当前值，可能变量不存在或格式不正确"
        echo "按回车键返回..."
        read
        return
    fi
    
    echo ""
    read -p "请输入新的值 (当前: $CURRENT_VALUE): " new_value
    
    if [ -z "$new_value" ]; then
        echo "操作取消"
        sleep 1
        return
    fi
    
    # 确认修改
    echo ""
    echo "您要将 \$$var_name 从:"
    echo "  '$CURRENT_VALUE'"
    echo "修改为:"
    echo "  '$new_value'"
    echo ""
    
    read -p "确认修改? (y/n): " confirm
    
    if [[ "$confirm" =~ ^[Yy]$ ]]; then
        # 获取原值的格式
        local current_line=$(grep "static[[:space:]]*\\\$$var_name" "$PHP_FILE" | head -1)
        local clean_line=$(echo "$current_line" | sed 's/\/\/.*//' | sed 's/#.*//')
        
        if [[ "$clean_line" =~ =\s*\"[^\"]*\" ]]; then
            # 原值是双引号字符串
            final_value="\"$new_value\""
        elif [[ "$clean_line" =~ =\s*\'[^\']*\' ]]; then
            # 原值是单引号字符串
            final_value="'$new_value'"
        elif [[ "$clean_line" =~ =\s*(true|false) ]]; then
            # 原值是布尔值
            if [[ "$new_value" =~ ^[Tt]rue$ ]]; then
                final_value="true"
            elif [[ "$new_value" =~ ^[Ff]alse$ ]]; then
                final_value="false"
            else
                final_value="$new_value"
            fi
        elif [[ "$clean_line" =~ =\s*[0-9]+ ]]; then
            # 原值是数字
            if [[ "$new_value" =~ ^[0-9]+$ ]]; then
                final_value="$new_value"
            else
                final_value="\"$new_value\""
            fi
        else
            # 默认使用双引号
            final_value="\"$new_value\""
        fi
        
        update_config "$var_name" "$final_value"
        # 更新内存中的变量值
        eval "$var_name=\"$new_value\""
    else
        echo "操作取消"
    fi
    
    echo "按回车键继续..."
    read
}

# 显示菜单
show_menu() {
    clear
    echo "=============================================="
    echo "      HUSTOJ 配置文件管理工具"
    echo "=============================================="
    echo ""
    echo "请选择要修改的配置项:"
    echo ""
    echo "  1) 数据库配置"
    echo "  2) 系统基本配置"
    echo "  3) 邮件配置"
    echo "  4) 功能开关"
    echo "  5) 比赛相关配置"
    echo "  6) 安全与限制"
    echo "  7) 显示所有配置"
    echo "  8) 退出"
    echo ""
    read -p "请输入选项 [1-8]: " main_choice
}

# 数据库配置菜单
show_db_menu() {
    clear
    echo "=============================================="
    echo "        数据库配置"
    echo "=============================================="
    echo ""
    echo "  1) DB_HOST - 数据库服务器"
    if [ -n "$DB_HOST" ]; then
        echo "      当前值: $DB_HOST"
    fi
    echo ""
    echo "  2) DB_NAME - 数据库名"
    if [ -n "$DB_NAME" ]; then
        echo "      当前值: $DB_NAME"
    fi
    echo ""
    echo "  3) DB_USER - 数据库用户"
    if [ -n "$DB_USER" ]; then
        echo "      当前值: $DB_USER"
    fi
    echo ""
    echo "  4) DB_PASS - 数据库密码"
    if [ -n "$DB_PASS" ]; then
        echo "      当前值: $DB_PASS"
    fi
    echo ""
    echo "  5) 返回主菜单"
    echo ""
    read -p "请输入选项 [1-5]: " choice
    
    case $choice in
        1) edit_variable "DB_HOST" ;;
        2) edit_variable "DB_NAME" ;;
        3) edit_variable "DB_USER" ;;
        4) edit_variable "DB_PASS" ;;
        5) return ;;
        *) echo "无效选项"; sleep 1; show_db_menu ;;
    esac
}

# 系统基本配置菜单
show_basic_menu() {
    clear
    echo "=============================================="
    echo "        系统基本配置"
    echo "=============================================="
    echo ""
    echo "  1) OJ_NAME - 系统名称"
    if [ -n "$OJ_NAME" ]; then
        echo "      当前值: $OJ_NAME"
    fi
    echo ""
    echo "  2) OJ_HOME - 主页目录"
    if [ -n "$OJ_HOME" ]; then
        echo "      当前值: $OJ_HOME"
    fi
    echo ""
    echo "  3) OJ_ADMIN - 管理员邮箱"
    if [ -n "$OJ_ADMIN" ]; then
        echo "      当前值: $OJ_ADMIN"
    fi
    echo ""
    echo "  4) OJ_DATA - 测试数据目录"
    if [ -n "$OJ_DATA" ]; then
        echo "      当前值: $OJ_DATA"
    fi
    echo ""
    echo "  5) OJ_LANG - 默认语言"
    if [ -n "$OJ_LANG" ]; then
        echo "      当前值: $OJ_LANG"
    fi
    echo ""
    echo "  6) OJ_CSS - 主题样式"
    if [ -n "$OJ_CSS" ]; then
        echo "      当前值: $OJ_CSS"
    fi
    echo ""
    echo "  7) OJ_TEMPLATE - 模板"
    if [ -n "$OJ_TEMPLATE" ]; then
        echo "      当前值: $OJ_TEMPLATE"
    fi
    echo ""
    echo "  8) 返回主菜单"
    echo ""
    read -p "请输入选项 [1-8]: " choice
    
    case $choice in
        1) edit_variable "OJ_NAME" ;;
        2) edit_variable "OJ_HOME" ;;
        3) edit_variable "OJ_ADMIN" ;;
        4) edit_variable "OJ_DATA" ;;
        5) edit_variable "OJ_LANG" ;;
        6) edit_variable "OJ_CSS" ;;
        7) edit_variable "OJ_TEMPLATE" ;;
        8) return ;;
        *) echo "无效选项"; sleep 1; show_basic_menu ;;
    esac
}

# 邮件配置菜单
show_mail_menu() {
    clear
    echo "=============================================="
    echo "        邮件配置"
    echo "=============================================="
    echo ""
    echo "  1) SMTP_SERVER - SMTP服务器"
    if [ -n "$SMTP_SERVER" ]; then
        echo "      当前值: $SMTP_SERVER"
    fi
    echo ""
    echo "  2) SMTP_PORT - SMTP端口"
    if [ -n "$SMTP_PORT" ]; then
        echo "      当前值: $SMTP_PORT"
    fi
    echo ""
    echo "  3) SMTP_USER - SMTP用户"
    if [ -n "$SMTP_USER" ]; then
        echo "      当前值: $SMTP_USER"
    fi
    echo ""
    echo "  4) SMTP_PASS - SMTP密码"
    if [ -n "$SMTP_PASS" ]; then
        echo "      当前值: $SMTP_PASS"
    fi
    echo ""
    echo "  5) OJ_MAIL - 启用内邮"
    if [ -n "$OJ_MAIL" ]; then
        echo "      当前值: $OJ_MAIL"
    fi
    echo ""
    echo "  6) 返回主菜单"
    echo ""
    read -p "请输入选项 [1-6]: " choice
    
    case $choice in
        1) edit_variable "SMTP_SERVER" ;;
        2) edit_variable "SMTP_PORT" ;;
        3) edit_variable "SMTP_USER" ;;
        4) edit_variable "SMTP_PASS" ;;
        5) edit_variable "OJ_MAIL" ;;
        6) return ;;
        *) echo "无效选项"; sleep 1; show_mail_menu ;;
    esac
}

# 功能开关菜单
show_feature_menu() {
    clear
    echo "=============================================="
    echo "        功能开关"
    echo "=============================================="
    echo ""
    echo "  1) OJ_BBS - 论坛"
    if [ -n "$OJ_BBS" ]; then
        echo "      当前值: $OJ_BBS"
    fi
    echo ""
    echo "  2) OJ_ONLINE - 记录在线"
    if [ -n "$OJ_ONLINE" ]; then
        echo "      当前值: $OJ_ONLINE"
    fi
    echo ""
    echo "  3) OJ_SIM - 显示相似度"
    if [ -n "$OJ_SIM" ]; then
        echo "      当前值: $OJ_SIM"
    fi
    echo ""
    echo "  4) OJ_DICT - 在线翻译"
    if [ -n "$OJ_DICT" ]; then
        echo "      当前值: $OJ_DICT"
    fi
    echo ""
    echo "  5) OJ_ACE_EDITOR - 代码高亮"
    if [ -n "$OJ_ACE_EDITOR" ]; then
        echo "      当前值: $OJ_ACE_EDITOR"
    fi
    echo ""
    echo "  6) OJ_AUTO_SHARE - 自动分享代码"
    if [ -n "$OJ_AUTO_SHARE" ]; then
        echo "      当前值: $OJ_AUTO_SHARE"
    fi
    echo ""
    echo "  7) OJ_VCODE - 验证码"
    if [ -n "$OJ_VCODE" ]; then
        echo "      当前值: $OJ_VCODE"
    fi
    echo ""
    echo "  8) OJ_REGISTER - 允许注册"
    if [ -n "$OJ_REGISTER" ]; then
        echo "      当前值: $OJ_REGISTER"
    fi
    echo ""
    echo "  9) OJ_DOWNLOAD - 允许下载数据"
    if [ -n "$OJ_DOWNLOAD" ]; then
        echo "      当前值: $OJ_DOWNLOAD"
    fi
    echo ""
    echo " 10) OJ_TEST_RUN - 测试运行"
    if [ -n "$OJ_TEST_RUN" ]; then
        echo "      当前值: $OJ_TEST_RUN"
    fi
    echo ""
    echo " 11) OJ_REMOTE_JUDGE - 远程评测"
    if [ -n "$OJ_REMOTE_JUDGE" ]; then
        echo "      当前值: $OJ_REMOTE_JUDGE"
    fi
    echo ""
    echo " 12) 返回主菜单"
    echo ""
    read -p "请输入选项 [1-12]: " choice
    
    case $choice in
        1) edit_variable "OJ_BBS" ;;
        2) edit_variable "OJ_ONLINE" ;;
        3) edit_variable "OJ_SIM" ;;
        4) edit_variable "OJ_DICT" ;;
        5) edit_variable "OJ_ACE_EDITOR" ;;
        6) edit_variable "OJ_AUTO_SHARE" ;;
        7) edit_variable "OJ_VCODE" ;;
        8) edit_variable "OJ_REGISTER" ;;
        9) edit_variable "OJ_DOWNLOAD" ;;
        10) edit_variable "OJ_TEST_RUN" ;;
        11) edit_variable "OJ_REMOTE_JUDGE" ;;
        12) return ;;
        *) echo "无效选项"; sleep 1; show_feature_menu ;;
    esac
}

# 比赛相关配置菜单
show_contest_menu() {
    clear
    echo "=============================================="
    echo "        比赛相关配置"
    echo "=============================================="
    echo ""
    echo "  1) OJ_OI_MODE - OI比赛模式"
    if [ -n "$OJ_OI_MODE" ]; then
        echo "      当前值: $OJ_OI_MODE"
    fi
    echo ""
    echo "  2) OJ_RANK_LOCK_PERCENT - 封榜比例"
    if [ -n "$OJ_RANK_LOCK_PERCENT" ]; then
        echo "      当前值: $OJ_RANK_LOCK_PERCENT"
    fi
    echo ""
    echo "  3) OJ_RANK_LOCK_DELAY - 赛后封榜时间"
    if [ -n "$OJ_RANK_LOCK_DELAY" ]; then
        echo "      当前值: $OJ_RANK_LOCK_DELAY"
    fi
    echo ""
    echo "  4) OJ_SHOW_METAL - 显示奖牌"
    if [ -n "$OJ_SHOW_METAL" ]; then
        echo "      当前值: $OJ_SHOW_METAL"
    fi
    echo ""
    echo "  5) OJ_NOIP_KEYWORD - NOIP关键词"
    if [ -n "$OJ_NOIP_KEYWORD" ]; then
        echo "      当前值: $OJ_NOIP_KEYWORD"
    fi
    echo ""
    echo "  6) OJ_CONTEST_TOTAL_100 - 按100分计分"
    if [ -n "$OJ_CONTEST_TOTAL_100" ]; then
        echo "      当前值: $OJ_CONTEST_TOTAL_100"
    fi
    echo ""
    echo "  7) 返回主菜单"
    echo ""
    read -p "请输入选项 [1-7]: " choice
    
    case $choice in
        1) edit_variable "OJ_OI_MODE" ;;
        2) edit_variable "OJ_RANK_LOCK_PERCENT" ;;
        3) edit_variable "OJ_RANK_LOCK_DELAY" ;;
        4) edit_variable "OJ_SHOW_METAL" ;;
        5) edit_variable "OJ_NOIP_KEYWORD" ;;
        6) edit_variable "OJ_CONTEST_TOTAL_100" ;;
        7) return ;;
        *) echo "无效选项"; sleep 1; show_contest_menu ;;
    esac
}

# 安全与限制菜单
show_security_menu() {
    clear
    echo "=============================================="
    echo "        安全与限制配置"
    echo "=============================================="
    echo ""
    echo "  1) OJ_REG_SPEED - 注册频率限制"
    if [ -n "$OJ_REG_SPEED" ]; then
        echo "      当前值: $OJ_REG_SPEED"
    fi
    echo ""
    echo "  2) OJ_NEED_LOGIN - 需要登录"
    if [ -n "$OJ_NEED_LOGIN" ]; then
        echo "      当前值: $OJ_NEED_LOGIN"
    fi
    echo ""
    echo "  3) OJ_REG_NEED_CONFIRM - 注册需要审核"
    if [ -n "$OJ_REG_NEED_CONFIRM" ]; then
        echo "      当前值: $OJ_REG_NEED_CONFIRM"
    fi
    echo ""
    echo "  4) OJ_SUBMIT_COOLDOWN_TIME - 提交冷却时间"
    if [ -n "$OJ_SUBMIT_COOLDOWN_TIME" ]; then
        echo "      当前值: $OJ_SUBMIT_COOLDOWN_TIME"
    fi
    echo ""
    echo "  5) OJ_LIMIT_TO_1_IP - 单IP登录限制"
    if [ -n "$OJ_LIMIT_TO_1_IP" ]; then
        echo "      当前值: $OJ_LIMIT_TO_1_IP"
    fi
    echo ""
    echo "  6) OJ_PUBLIC_STATUS - 公开状态"
    if [ -n "$OJ_PUBLIC_STATUS" ]; then
        echo "      当前值: $OJ_PUBLIC_STATUS"
    fi
    echo ""
    echo "  7) OJ_RANK_HIDDEN - 隐藏排名用户"
    if [ -n "$OJ_RANK_HIDDEN" ]; then
        echo "      当前值: $OJ_RANK_HIDDEN"
    fi
    echo ""
    echo "  8) 返回主菜单"
    echo ""
    read -p "请输入选项 [1-8]: " choice
    
    case $choice in
        1) edit_variable "OJ_REG_SPEED" ;;
        2) edit_variable "OJ_NEED_LOGIN" ;;
        3) edit_variable "OJ_REG_NEED_CONFIRM" ;;
        4) edit_variable "OJ_SUBMIT_COOLDOWN_TIME" ;;
        5) edit_variable "OJ_LIMIT_TO_1_IP" ;;
        6) edit_variable "OJ_PUBLIC_STATUS" ;;
        7) edit_variable "OJ_RANK_HIDDEN" ;;
        8) return ;;
        *) echo "无效选项"; sleep 1; show_security_menu ;;
    esac
}

# 显示所有配置
show_all_config() {
    clear
    echo "=============================================="
    echo "        所有配置变量"
    echo "=============================================="
    echo ""
    
    # 显示数据库配置
    echo "数据库配置:"
    echo "  DB_HOST: ${DB_HOST:-未设置}"
    echo "  DB_NAME: ${DB_NAME:-未设置}"
    echo "  DB_USER: ${DB_USER:-未设置}"
    echo "  DB_PASS: ${DB_PASS:-未设置}"
    echo ""
    
    # 显示系统基本配置
    echo "系统基本配置:"
    echo "  OJ_NAME: ${OJ_NAME:-未设置}"
    echo "  OJ_HOME: ${OJ_HOME:-未设置}"
    echo "  OJ_ADMIN: ${OJ_ADMIN:-未设置}"
    echo "  OJ_DATA: ${OJ_DATA:-未设置}"
    echo "  OJ_LANG: ${OJ_LANG:-未设置}"
    echo "  OJ_CSS: ${OJ_CSS:-未设置}"
    echo "  OJ_TEMPLATE: ${OJ_TEMPLATE:-未设置}"
    echo ""
    
    # 显示邮件配置
    echo "邮件配置:"
    echo "  SMTP_SERVER: ${SMTP_SERVER:-未设置}"
    echo "  SMTP_PORT: ${SMTP_PORT:-未设置}"
    echo "  SMTP_USER: ${SMTP_USER:-未设置}"
    echo "  SMTP_PASS: ${SMTP_PASS:-未设置}"
    echo "  OJ_MAIL: ${OJ_MAIL:-未设置}"
    echo ""
    
    # 显示功能开关
    echo "功能开关:"
    echo "  OJ_BBS: ${OJ_BBS:-未设置}"
    echo "  OJ_ONLINE: ${OJ_ONLINE:-未设置}"
    echo "  OJ_SIM: ${OJ_SIM:-未设置}"
    echo "  OJ_DICT: ${OJ_DICT:-未设置}"
    echo "  OJ_ACE_EDITOR: ${OJ_ACE_EDITOR:-未设置}"
    echo "  OJ_AUTO_SHARE: ${OJ_AUTO_SHARE:-未设置}"
    echo "  OJ_VCODE: ${OJ_VCODE:-未设置}"
    echo "  OJ_REGISTER: ${OJ_REGISTER:-未设置}"
    echo "  OJ_DOWNLOAD: ${OJ_DOWNLOAD:-未设置}"
    echo "  OJ_TEST_RUN: ${OJ_TEST_RUN:-未设置}"
    echo "  OJ_REMOTE_JUDGE: ${OJ_REMOTE_JUDGE:-未设置}"
    echo ""
    
    # 显示比赛相关配置
    echo "比赛相关配置:"
    echo "  OJ_OI_MODE: ${OJ_OI_MODE:-未设置}"
    echo "  OJ_RANK_LOCK_PERCENT: ${OJ_RANK_LOCK_PERCENT:-未设置}"
    echo "  OJ_RANK_LOCK_DELAY: ${OJ_RANK_LOCK_DELAY:-未设置}"
    echo "  OJ_SHOW_METAL: ${OJ_SHOW_METAL:-未设置}"
    echo "  OJ_NOIP_KEYWORD: ${OJ_NOIP_KEYWORD:-未设置}"
    echo "  OJ_CONTEST_TOTAL_100: ${OJ_CONTEST_TOTAL_100:-未设置}"
    echo ""
    
    # 显示安全与限制配置
    echo "安全与限制配置:"
    echo "  OJ_REG_SPEED: ${OJ_REG_SPEED:-未设置}"
    echo "  OJ_NEED_LOGIN: ${OJ_NEED_LOGIN:-未设置}"
    echo "  OJ_REG_NEED_CONFIRM: ${OJ_REG_NEED_CONFIRM:-未设置}"
    echo "  OJ_SUBMIT_COOLDOWN_TIME: ${OJ_SUBMIT_COOLDOWN_TIME:-未设置}"
    echo "  OJ_LIMIT_TO_1_IP: ${OJ_LIMIT_TO_1_IP:-未设置}"
    echo "  OJ_PUBLIC_STATUS: ${OJ_PUBLIC_STATUS:-未设置}"
    echo "  OJ_RANK_HIDDEN: ${OJ_RANK_HIDDEN:-未设置}"
    echo ""
    
    echo "按回车键返回主菜单..."
    read
}

# 主循环
main() {
    check_config_file
    load_current_config
    
    while true; do
        show_menu
        
        case $main_choice in
            1) show_db_menu ;;
            2) show_basic_menu ;;
            3) show_mail_menu ;;
            4) show_feature_menu ;;
            5) show_contest_menu ;;
            6) show_security_menu ;;
            7) show_all_config ;;
            8)
                echo ""
                echo "感谢使用，再见！"
                exit 0
                ;;
            *)
                echo "无效选项，请重新选择"
                sleep 1
                ;;
        esac
    done
}

# 启动脚本
main
