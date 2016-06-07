mkdir -p /root/.ssh
cp /server/docker/deploykey /root/.ssh/id_rsa
cp /server/docker/known_hosts /root/.ssh/known_hosts
echo " IdentityFile ~/.ssh/id_rsa" >> /etc/ssh/ssh_config
chmod 600 /root/.ssh/id_rsa
