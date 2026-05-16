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


