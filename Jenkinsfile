node {
  def remote = [:]
  remote.name = 'localhost'
  remote.host = '172.17.8.48'
  remote.user = 'root'
  remote.password = 'Control2022*'
  remote.allowAnyHosts = true
  sshagent (credentials: ['Natalia_Pruebas']) {
    stage('Conexion_server') {
     sh 'cd /var/www/mios/mios-backend/pruebasnata-back-v2'
     sh 'git status'
      }
    stage('Test1') {
     sh 'echo "inicio test"'
  }
    stage('Test1') {
     sh 'echo "inicio test'
  }
}
}
