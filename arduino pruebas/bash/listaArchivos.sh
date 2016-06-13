#!/bin/bash
path=/home/pi/Documents/Arduino
cd $path
array=($(ls -d */))
for i in ${array[@]}; do
	echo $i
done

