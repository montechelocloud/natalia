pipeline {
    agent any
    stages {
        stage('Build') {
            steps {
                echo 'Start Building'
                echo 'Finish Building'
            }
        }
        stage('Test') {
            steps {
                echo 'Start Testing 1'
                - cat .env
                echo "Finish Testing 1"
            }
        }
        stage('Test2') {
            steps {
                echo 'Start Testing 2'
                echo "Finish Testing 2"
            }
        }
        stage('Deploy') {
            steps {
                echo 'Start Deploy'
                echo ''
            }
        }

        stage('Deploy2') {
            steps {
                echo 'Start Deploy'
                echo ''
            }
        }
    }
}
