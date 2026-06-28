# Docker Compose PHP App with Jenkins Deployment

This repository contains a Docker Compose stack for a PHP application using:
- Nginx 1.25
- PHP-FPM 7.3
- MySQL 8.0
- phpMyAdmin
- Jenkins for CI/CD deployment

## Files
- `docker-compose.yml` - application stack configuration
- `docker-compose-jenkins.yml` - Jenkins stack configuration
- `Dockerfile` - PHP-FPM image build file
- `Dockerfile-jenkins` - Jenkins image build file with Docker support
- `Jenkinsfile` - pipeline definition for building and deploying with Docker Compose
- `default.conf` - Nginx site configuration
- `index.php` - sample PHP app

## Run locally with Docker Compose

From the repo root:

```powershell
docker compose up --build -d
```

Open these URLs:
- App: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`

To stop and remove app containers and volumes:

```powershell
docker compose down -v
```

### Note about bind-mounts

The app and nginx images now contain the application files and `default.conf` baked into the images at build time. This avoids bind-mounting files from the Jenkins workspace at runtime and prevents the deployment from accidentally affecting the Jenkins container.

## Jenkins setup

Jenkins should run from its own compose file so app deployment does not restart the Jenkins container.

### Start Jenkins separately
```powershell
docker compose -f docker-compose-jenkins.yml up --build -d
```

Open Jenkins at:
- `http://localhost:8082`

### Start the app stack separately
```powershell
docker compose up --build -d
```

### Stop the app stack without stopping Jenkins
```powershell
docker compose down -v
```

### Stop Jenkins
```powershell
docker compose -f docker-compose-jenkins.yml down -v
```

## Jenkins setup

### Option 1: Run Jenkins from Docker Compose
This repo includes Jenkins in `docker-compose.yml`. Start the stack with:

```powershell
docker compose up --build -d
```

Then open Jenkins at `http://localhost:8082`.

### Option 2: Run Jenkins separately
Build the Jenkins image:

```powershell
docker build -t jenkins-with-docker -f Dockerfile-jenkins .
```

Run Jenkins with Docker socket access:

```powershell
docker run -d --name jenkins \
  -p 8082:8080 \
  -p 50000:50000 \
  -v jenkins_home:/var/jenkins_home \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v "${PWD}:/var/jenkins_home/workspace/docker-compose-php" \
  jenkins-with-docker
```

### Configure Jenkins job

#### Single Pipeline
1. Create a new Jenkins job: `Pipeline`
2. Select `Pipeline script from SCM`
3. Choose `Git` and enter your GitHub repository URL
4. Set `Script Path` to `Jenkinsfile`
5. Save and run the job

#### Multibranch Pipeline
1. Install `GitHub Branch Source` plugin
2. Create a new job: `Multibranch Pipeline`
3. Add a `GitHub` branch source with your repo URL
4. Configure credentials or GitHub App
5. Save and scan repository

### Trigger builds from GitHub

Use a GitHub webhook to trigger Jenkins when you push code:
- Repo Settings → Webhooks → Add webhook
- Payload URL: `http://<jenkins-host>:8082/github-webhook/`
- Content type: `application/json`
- Select `Just the push event`

## How it works

The `Jenkinsfile` performs these steps:
1. checkout code from GitHub
2. build Docker images using `docker compose build`
3. deploy the stack with `docker compose down -v` and `docker compose up -d --build`

## Notes
- Jenkins needs access to the Docker socket to run Docker commands.
- If using GitHub, make sure Jenkins has permission to access the repository.
- The app uses MySQL service credentials from `docker-compose.yml`.
