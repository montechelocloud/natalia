pipeline {
    agent any
    stages {
        stage('Build') {
            steps {
                echo 'Start Building'
                - git status
                - git checkout master
                echo 'Finish Building'
            }
        }
        stage('Test') {
            steps {
                echo 'Start Testing 1'
                - git status
                echo "Finish Testing 1"
            }
        }
        stage('Test2') {
            steps {
                echo 'Start Testing 2'
                - git status
                echo "Finish Testing 2"
            }
        }
        stage('Deploy') {
            steps {
                echo 'Start Deploy'
                - git status
                - git pull origin master
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
