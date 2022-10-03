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
                echo "Finish Testing 1"
            }
        }
        stage('Test') {
            steps {
                echo 'Compiling the code'
            }
        }
        stage('Deploy') {
            steps {
                echo 'Start Deploy'
            }
        }
    }
}

pipeline {
    agent any
    stages {
        stage('Stage 1') {
            steps {
                echo 'Compiling the code'
            }
        }
    }
}
