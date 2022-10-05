node {
  def remote = [:]
  remote.name = 'Nata_Pruebas'
  remote.host = '172.17.8.48'
  remote.user = 'root'
  remote.password = 'Control2022*'
  remote.allowAnyHosts = true

  stage('Conexion_server') {
    sh 'cd /var/www/mios/mios-backend/pruebasnata-back-v2'
    sh 'git status'
  }

  stage('Test1') {
    sshCommand remote: remote, command: "echo "inicio test""
  }

  stage('Deploy') {
    sshCommand remote: remote, command: "git pull"
    sshCommand remote: remote, command: "php artisan cache:clear"
  }
}
