#!/bin/bash
if [ "$1" = "" ]; then
   echo "Usage: $0 standard-execute-binary"
   echo "Example: after compiled your standard program by 'gcc -o main main.cc' "
   echo "         TYPE ---> '$0 main' "
   echo "         will generate .out files for each .in files in "`pwd`
   EXEC="./Main"
else
   EXEC="./$1"
fi
if [ "$EUID" -eq 0 ]; then
    echo "Error: This script should not be run as root." >&2
    exit 1
fi
if [ -f "Gen.py" ]; then
    echo "Found Gen.py, running it..."
    python3 Gen.py
    echo "Gen.py finished."
fi

if [ -f "Main.c" ]; then
    gcc -Wall -static -DONLINE_JUDGE -lm -o  "$EXEC"  Main.c
    echo "Compiled Main.c to Main using gcc with -Wall -static -DONLINE_JUDGE -lm"
elif [ -f "Main.cc" ]; then
    g++ -Wall -static -DONLINE_JUDGE -lm -o  "$EXEC"  Main.cc
    echo "Compiled Main.cc to Main using g++ with -Wall -static -DONLINE_JUDGE -lm"
else
    echo "Error: Neither Main.c nor Main.cc found" >&2
   
fi

if [ -f "interactor.cc" ]; then
    g++ --static -o interactor interactor.cc ||  g++ -o interactor interactor.cc
fi
if [ -f "spj.cc" ]; then
    g++ --static -o spj spj.cc ||  g++ -o spj spj.cc
fi
if [ -f "tpj.cc" ]; then
    g++ --static -o tpj tpj.cc ||  g++ -o tpj tpj.cc
fi
if [ -f "upj.cc" ]; then
    g++ --static -o upj upj.cc ||  g++ -o upj upj.cc
fi

# Kattis / DOMjudge 题目包（spj=4）：导入器把 output validator 的源码放在 validator.src/ 下，
# 判题机要的是本目录里的 ./validator。这里能起编译器是因为脚本本来就跑在判题机上、
# 且由准备数据的用户执行，web 进程不碰编译。
# validate.h 用的是 std::filesystem（C++17），作者写的 interactor 里还常见 std::ssize（C++20），
# 所以先 c++20 再退 c++17；-static 是因为判题容器里未必有同版本 libstdc++，编不过再退动态。
# 同目录的 .cpp 一起链：有的包把校验器拆成几个翻译单元。先编到 validator.tmp，成功了才覆盖，
# 免得半途失败留下个不能用的 validator 把整道题判成 RE。
if [ -d "validator.src" ]; then
    for SRC in `ls validator.src/output_validator/*.cc validator.src/output_validator/*.cpp \
                       validator.src/output_validators/*/*.cc validator.src/output_validators/*/*.cpp 2>/dev/null`
    do
        DIR=`dirname $SRC`
        SRCS=`ls $DIR/*.cc $DIR/*.cpp 2>/dev/null | tr '\n' ' '`
        if g++ -std=c++20 -O2 --static -I$DIR -o validator.tmp $SRCS 2>/dev/null \
           || g++ -std=c++17 -O2 --static -I$DIR -o validator.tmp $SRCS 2>/dev/null \
           || g++ -std=c++20 -O2 -I$DIR -o validator.tmp $SRCS 2>/dev/null \
           || g++ -std=c++17 -O2 -I$DIR -o validator.tmp $SRCS ; then
            mv -f validator.tmp validator
            chmod 755 validator
            echo "compiled ./validator from $SRCS"
            break
        else
            rm -f validator.tmp
            echo "compile ./validator from $SRCS failed" >&2
        fi
    done
fi

for INFILE in `ls *.in`
do
        OUTFILE=`basename -s .in $INFILE`.out
    if [ -f "$EXEC" ]; then
        if $EXEC < $INFILE > $OUTFILE ; then
                echo "make out for $INFILE -> $OUTFILE <br>"
        else
                echo "make out for $INFILE .....failed"
        fi
    fi
done


