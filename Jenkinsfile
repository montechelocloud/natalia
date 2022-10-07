pipeline{
   agent any
   stages{
      stage('login server'){
         steps{
            sshagent(credentials:['Root+Passwd']){
               sh 'ssh  -o StrictHostKeyChecking=no  root@172.17.8.48 uptime "whoami"'
            // sh 'ssh root@172.17.8.48'
          }
        echo "success lgoin"
         }
       }
   }
}