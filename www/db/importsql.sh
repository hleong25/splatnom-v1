#!/bin/sh

# usage:
# gunzip -c file.gz | mysql -h[database host] -u[username] -p[password] [database-name]

hostname=localhost
username=hleong25_testusr
dbname=hleong25_testimport

file1=./menu/menu.120313.1.static.sql.gz
file2=./menu/menu.120321.2.tables.sql.gz
file3=./menu/menu.120313.3.views.sql.gz

view_old_user=hleong25
view_new_user=$username

echo Importing $file1...
gunzip -c $file1 | mysql -h $hostname -u $username -p $dbname

echo Importing $file2...
gunzip -c $file2 | mysql -h $hostname -u $username -p $dbname

echo Importing $file3...
gunzip -c $file3 | sed "s/$view_old_user/$view_new_user/g" | mysql -h $hostname -u $username -p $dbname


