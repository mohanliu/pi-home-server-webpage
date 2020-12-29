#! /bin/bash

cd ./html
rm -rf *
cd ..

for file in `find /var/www/html/ -user root`
    do
    if [ ! -d $file ]
       then
       echo "Copying " $file "..."
       rsync -R $file ./html
    fi
done

cd ./html
mv var/www/html/* .
rm -rf var
cd ..

echo "Copying bash scripts..."
cp ~/bin/* bin 
