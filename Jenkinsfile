pipeline {
    agent any
    stages {
      stage('Test SSH') {
          echo 'Inicio Conexion'
          sh 'ssh root@172.17.8.48 cd /var/www/mios/mios-backend/pruebasnata-back-v2'
          sh 'ssh root@172.17.8.48 ll'
          sh 'echo 'Fin Conexion''
        }
}
}
