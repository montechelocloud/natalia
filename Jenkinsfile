pipeline {
    agent any
    def remote = [:]
    remote.name = 'Soul-Pruebas'
    remote.host = '172.17.8.48'
    remote.user = 'root'
    remote.password = 'Control2022*'
    remote.allowAnyHosts = true
    stage('Remote SSH') {
      sshCommand remote: remote, command: "ls -lrt"

        }
}
