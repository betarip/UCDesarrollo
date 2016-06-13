#!/bin/bash
for ip in `cat accesos.txt`; do
   ssh-copy-id -i ~/.ssh/id_rsa.pub $ip
#echo $ip
done
