pipeline{
   agent any
   stages{
      stage('login server'){
         steps{
            sshagent(credentials:['Control2022*']){
               sh 'ssh  -o StrictHostKeyChecking=no  root@172.17.8.48 uptime "whoami"'
          }
        echo "success lgoin"
         }
       }
   }
}