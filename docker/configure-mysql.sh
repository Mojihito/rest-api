/usr/sbin/mysqld &
sleep 5
echo "GRANT ALL ON *.* TO root@'localhost' IDENTIFIED BY 'root' WITH GRANT OPTION; FLUSH PRIVILEGES" | mysql
