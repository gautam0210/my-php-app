pipeline {
  agent any

  environment {
    COMPOSE_PROJECT_NAME = 'docker-compose-php'
  }

  stages {
    stage('Build Images') {
      steps {
        sh 'docker compose build'
      }
    }

    stage('Deploy') {
      steps {
        sh 'docker compose down -v'
        sh 'docker compose up -d --build'
      }
    }
  }

  post {
    success {
      echo 'Deployment completed successfully.'
    }
    failure {
      echo 'Deployment failed. Check the Jenkins logs for details.'
    }
  }
}
