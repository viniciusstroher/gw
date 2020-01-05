#INSTALAR ADB
apt-get install android-tools-adb android-tools-fastboot
instalar node e pm2

curl -sL https://deb.nodesource.com/setup_10.x -o nodesource_setup.sh
bash nodesource_setup.sh
npm instalal -g pm2

#Colocar crons no servidor
#pm2
pm2 start /var/www/html/gw/crons/cronAddContacts.php --cron "*/1 * * * *"
pm2 start /var/www/html/gw/crons/cronGetContacts.php --cron "*/1 * * * *"

#ou crontab
#1 * * * * /usr/bin/php /var/www/html/gw/crons/cronAddContacts.php
#1 * * * * /usr/bin/php /var/www/html/gw/crons/cronGetContacts.php


#habilitar (apache) - para pegar ip do cliente  header:x-forwareded-for
mod_custom_header

######################################################################

#Insert - Consultar
HEADER:
Authorization: Basic base64_encode($usuario:$password)

Method: 
GET

IP: 
http://<IP>/gw/api.php?action=checkNumber&number=<PAIS><DD><NUMBER>

RETORNO:
{
    "data": [
        {
            "id": "1",
            "numbers": " 55 51 95412459",
            "whats": "t",
            "status": "UNCHECKED",
            "created_at": "2020-01-04 18:02:35",
            "updated_at": "2020-01-04 18:02:35"
        }
    ]
}

status -> UNCHECKED - NAO PASSOU PELA CRON DE ADICIONAR NO CELULAR
		  ADDED - DEPOIS QUE A CRON cronAddContacts adicionou o numero no android
		  CHECKED - DEPOIS QUE ELE VIU SE TEM OU NAO NUMERO DE WHATS

whats -> true - tem whatsapp
		 false - nao tem whatsapp