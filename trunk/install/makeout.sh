#!/bin/bash
if [ "$1" = "" ]; then
   echo "Usage: $0 standard-execute-binary"
   echo "Example: after compiled your standard program by 'gcc -o main main.cc' "
   echo "         TYPE ---> '$0 main' "
   echo "         will generate .out files for each .in files in "`pwd`
   exit 1
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

EXEC="./Main"

if [ -f "Main.c" ]; then
    gcc -Wall -static -DONLINE_JUDGE -lm -o Main Main.c
    echo "Compiled Main.c to Main using gcc with -Wall -static -DONLINE_JUDGE -lm"
elif [ -f "Main.cc" ]; then
    g++ -Wall -static -DONLINE_JUDGE -lm -o Main Main.cc
    echo "Compiled Main.cc to Main using g++ with -Wall -static -DONLINE_JUDGE -lm"
else
    echo "Error: Neither Main.c nor Main.cc found" >&2
    EXEC="./$1"
fi


for INFILE in `ls *.in`
do
        OUTFILE=`basename -s .in $INFILE`.out
        if $EXEC < $INFILE > $OUTFILE ; then
                echo "make out for $INFILE -> $OUTFILE <br>"
        else
                echo "make out for $INFILE .....failed"
        fi
done

