pipeline{
   agent any
   stages{
      stage('login server'){
         steps{
            sshagent(credentials:['nataliapruebas']){
             sh 'ssh -o StrictHostKeyChecking=no 172.17.8.48 -l root whoami'
          }
       echo 'success login'
         }
       }
   }
}

// pipeline{
//    agent any
//    stages{
//       stage('login server'){
//          steps{
//             sshagent(credentials:['nataliapruebas']){
//              sh 'ssh 172.17.8.48 whoami'
//           }
//        echo 'success login'
//          }
//        }
//    }
// }