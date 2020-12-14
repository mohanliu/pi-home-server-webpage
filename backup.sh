#! /bin/bash

for file in `find /var/www/html/ -user root`
    do
    echo "Copying " $file "..."
    cp $file ./html
done

cp ~/bin/* bin 
