# Webservice for Smart Supervision

## Despliegue

### Colas de Trabajo

#### Colas que se Requieren

- get_complaints

#### Instalar Supervisor

**Ubuntu:**

Actualizar el sistema:

```bash
sudo apt-get update
sudo apt-get upgrade
```

Instalar Supervisor:

```bash
sudo apt-get install supervisor
```

**Centos:**

Actualizar el sistema:

```bash
sudo yum update -y
```

Como Supervisor no esta disponible en el repositorio oficial de Centos, instalamos el repositorio EPEL con el comando:

```bash
sudo yum install epel-release
```

Actualizar el sistema:

```bash
sudo yum update -y
```

Instalar Supervisor:

```bash
sudo apt-get install supervisor
```

Iniciamos y habilitamos el demonio del Supervisor para que se inicie al arranque del sistema con los siguientes comandos:

```bash
sudo systemctl start supervisord
sudo systemctl enable supervisord
```

Verificamos el estado con:

```bash
sudo systemctl status supervisord
```

#### Configurar los trabajos

- Ubuntu: Las configuraciones del supervisor se almacenan en el directorio **/etc/supervisor/conf.d**.

- Centos: Las configuraciones del supervisor se almacenan en el directorio **/etc/supervisord.d**

Despues de ubicar el directorio se creara un archivo por [cola que se requiera](#colas-que-se-requieren), en `[program:nombre_de_la_cola]` y `queue:nombre_de_la_cola`, reemplazar "**nombre_de_la_cola**" por el nombre de la cola que se vaya a ejecutar; en `user=nombre_usuario` reemplazar "**nombre_usuario**" por el usuario que ejecutara el proceso o tendrá los permisos; en `command=php ruta_absoluta/artisan` y `stdout_logfile=ruta_absoluta/worker.log` reemplazar "**ruta_absoluta**" por la ruta absoluta donde se encuentra ubicado el proyecto. La estructura del archivo es la siguiente:

```shell
[program:nombre_de_la_cola]
process_name=%(program_name)s_%(process_num)02d
command=php ruta_absoluta/artisan queue:nombre_de_la_cola --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=nombre_usuario
numprocs=8
redirect_stderr=true
stdout_logfile=ruta_absoluta/worker.log
stopwaitsecs=3600
```

#### Iniciar el Supervisor

Usamos la siguiente secuencia de comandos:

```bash
sudo supervisorctl reread
```

```bash
sudo supervisorctl update
```

#### Iniciar las Colas

Para iniciar un solo trabajo usamos:

```bash
sudo supervisorctl start nombre_del_trabajo:*
```

o:

```bash
sudo supervisorctl start nombre_del_trabajo
```

Para iniciar todos usamos:

```bash
sudo supervisorctl start all
```

#### Reiniciar Colas

Para reiniciar todas las colas usamos:

```bash
php artisan queue:restart
```

#### Detener Colas

Para detener un solo trabajo usamos:

```bash
sudo supervisorctl stop nombre_del_trabajo
```

o para detener todas:

```bash
sudo supervisorctl stop all
```
