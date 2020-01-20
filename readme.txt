#ANDROID
#no arquivo .env esta a conexao do postgres e do adb
#o adb é o protocolo de connectar a um android
#o android usado é um padrao do genymotion
#nele esta instalados os contatos e o whatsappbusiness
#tambem esta habilitado o modo desenvolvedor dele
#Verificar a data da ultima atualizacao da conta em contatos (contas)

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
pm2 start /var/www/html/gw/crons/cronCheckAdb.php --cron "*/1 * * * *"

#installar cron se for container
#apt-get install cron
#ou crontab
service cron start

#* * * * * /usr/bin/php /var/www/html/gw/crons/cronAddContacts.php
#* * * * * /usr/bin/php /var/www/html/gw/crons/cronGetContacts.php
#* * * * * /usr/bin/php /var/www/html/gw/crons/cronCheckAdb.php


#habilitar (apache) - para pegar ip do cliente  header:x-forwareded-for
mod_custom_header


#####################################################################
#criar usuario
php cli/createUser.php <user> <password>
Retorno: Basic <token>

#deletar usuario
php cli/deleteUser.php <user> <password>
Retorno: 

#pegar token
php cli/generateToken.php <user> <password>
Retorno: Basic <token>

######################################################################

Sistema depende do adb e as vezes o genymotion derruba.
COLOCAR MONITORAMENTO NO ZABBIX (telnet <ip_mobile> 5555)

##################

#Insert - Consultar
HEADER:
Authorization: Basic base64_encode($usuario:$password)

Method: 
POST

IP: 
http://<IP>/gw/api.php
action=checkNumber
number=<NUMBER>
ddd=<DDD>
ddi=<DDI> (optional) (nao enviar com +)
group=optional for grouping
EX:
                                                 DDNNNNNNNN
http://<IP>/gw/api.php?action=checkNumber&ddi=55&ddd=51&number=95412459

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