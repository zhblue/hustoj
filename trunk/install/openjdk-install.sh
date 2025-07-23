#!/bin/bash

# OpenJDK install script by mxd.
if [ -f /etc/os-release ]; then
    . /etc/os-release
    case "$ID" in
        debian|ubuntu)
            DISTRO="debian-ubuntu"
            ;;
        fedora)
            DISTRO="fedora"
            ;;
        centos|rhel)
            DISTRO="centos-rhel"
            ;;
        alpine)
            DISTRO="alpine"
            ;;
        arch)
            echo "Arch Linux NOT SUPPORT."
            exit 1
            ;;
        *)
            echo "Unsupported Linux distributions: $ID"
            exit 1
            ;;
    esac
else
    echo "Unable to detect distribution. exit...."
    exit 1
fi

echo "请选择要安装的 OpenJDK 版本："
echo "Please choose the OpenJDK version to install:"
echo "1. 8"
echo "2. 11"
echo "3. 17"
echo "4. 21"
read -p "请输入您的选择 (1-4)： Enter your choice (1-4): " choice

case $choice in
    1) VERSION=8 ;;
    2) VERSION=11 ;;
    3) VERSION=17 ;;
    4) VERSION=21 ;;
    *) echo "无效的选择。退出。Invalid choice. Exiting."; exit 1 ;;
esac

if [ "$DISTRO" = "debian-ubuntu" ]; then
    sudo apt-get update
    sudo apt-get install -y wget apt-transport-https
    wget -qO - https://packages.adoptium.net/artifactory/api/gpg/key/public | sudo gpg --dearmor | sudo tee /etc/apt/trusted.gpg.d/adoptium.gpg > /dev/null
    echo "deb https://mirrors.cernet.edu.cn/Adoptium/deb $(awk -F= '/^VERSION_CODENAME/{print$2}' /etc/os-release) main" | sudo tee /etc/apt/sources.list.d/adoptium.list
    sudo apt-get update
    sudo apt-get install -y temurin-${VERSION}-jdk
elif [ "$DISTRO" = "fedora" ] || [ "$DISTRO" = "centos-rhel" ]; then
    sudo tee /etc/yum.repos.d/adoptium.repo > /dev/null <<EOF
[Adoptium]
name=Adoptium
baseurl=https://mirrors.cernet.edu.cn/Adoptium/rpm/${ID}/${VERSION_ID}/\$basearch
enabled=1
gpgcheck=1
gpgkey=https://packages.adoptium.net/artifactory/api/gpg/key/public
EOF
    if [ "$DISTRO" = "fedora" ]; then
        sudo dnf install -y temurin-${VERSION}-jdk
    else
        if [ "$ID" = "centos" ] && [ "${VERSION_ID%%.*}" -eq 7 ]; then
            sudo yum install -y temurin-${VERSION}-jdk
        elif [ "$ID" = "rhel" ] && [ "${VERSION_ID%%.*}" -eq 7 ]; then
            sudo yum install -y temurin-${VERSION}-jdk
        else
            sudo dnf install -y temurin-${VERSION}-jdk
        fi
    fi
elif [ "$DISTRO" = "alpine" ]; then
    sudo wget -O /etc/apk/keys/adoptium.rsa.pub https://packages.adoptium.net/artifactory/api/security/keypair/public/repositories/apk
    echo 'https://packages.adoptium.net/artifactory/apk/alpine/main' | sudo tee -a /etc/apk/repositories
    sudo apk update
    sudo apk add temurin-${VERSION}-jdk
fi