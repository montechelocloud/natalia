pipeline{
   agent any
   stages{
      stage('login server'){
         steps{
            sshagent(credentials:['nataliapruebas']){
             sh 'ssh -T root@172.17.8.48'
            // sh 'ssh -t root@172.17.8.48 '$(< cd /var/www/mios/mios-backend/pruebasnata-back-v2''
          }
      echo 'success login'
         }
       }
   }
}