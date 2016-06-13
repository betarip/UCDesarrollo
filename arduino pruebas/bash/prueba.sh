#! /bin/bash
PATH=
if [ $# -eq 0 ]
then
	echo "0"
else
	rm "test"
	ls -l
	echo $?
	if [ $? -eq 0 ]
	then
		echo  "0"
        else
                echo  "1"
        fi
fi
