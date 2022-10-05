node {
  def remote = [:]
  remote.name = 'Nata_Pruebas'
  remote.host = '172.17.8.48'
  remote.user = 'root'
  remote.password = 'Control2022*'
  remote.allowAnyHosts = true

  stage('Conexion_server') {
    sshCommand remote: remote, command: "echo "conexion establecida""
    sshCommand remote: remote, command: "cd /var/www/mios/mios-backend/pruebasnata-back-v2"
    sshCommand remote: remote, command: "git status"
  }

  stage('Test1') {
    sshCommand remote: remote, command: "echo "inicio test""
  }

  stage('Deploy') {
    sshCommand remote: remote, command: "git pull"
    sshCommand remote: remote, command: "php artisan cache:clear"
  }
}
