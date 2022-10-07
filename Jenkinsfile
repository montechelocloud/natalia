pipeline {
  agent'Soul-Pruebas' {
      stage('Conexion_server') {
              sh "pwd"
              sh "cd /var/www/mios/mios-backend/pruebasnata-back-v2"
              sh "ll"
    }

            stage('Test1') {
              sh "echo "inicio test""
    }

          stage('Deploy') {
            sh "git pull"
            sh"php artisan cache:clear"
    }

          }
      }
  