pipeline {
    agent any
      def remote = [:]
      remote.name = 'Natalia'
      remote.host = '172.17.8.48'
      remote.user = 'root'
      remote.password = 'Control2022*'
      remote.allowAnyHosts = true
      stage('Conexion SSH') {
        sshCommand remote: remote, command: "pwd"
        sshCommand remote: remote, command: "cd /var/www/mios/mios-backend/pruebasnata-back-v2"
      }
      stage('Test SSH') {
        sshCommand remote: remote, command: "echo "Conexion""
      }
      stage('Deploy SSH') {
        sshCommand remote: remote, command: "echo "Conexion Finish""
}