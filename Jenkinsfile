pipeline {
    agent {
    node {
        label 'Soul-Pruebas'
        customWorkspace '/some/other/path'
        stages {
          stage('Conexion_server') {
             sshCommand remote: remote, command: "pwd"
             sshCommand remote: remote, command: "cd /var/www/mios/mios-backend/pruebasnata-back-v2"
             sshCommand remote: remote, command: "ll"
  }

          stage('Test1') {
            sshCommand remote: remote, command: "echo "inicio test""
  }

         stage('Deploy') {
           sshCommand remote: remote, command: "git pull"
           sshCommand remote: remote, command: "php artisan cache:clear"
  }
}
    }
}
}
