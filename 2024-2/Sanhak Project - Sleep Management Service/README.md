# SLEEPDIVER(개발 중..)
This Sleep Management Service analyzes users' sleep patterns by processing sleep stage data through chat-GPT. Using connected IoT devices, the service identifies sleep disturbance factors and provides personalized insights to improve sleep quality..

## Features
- **User Management**: Manage users and their devices.
- **Sleep Data Recording**: Record and store sleep data from user devices.
- **Sleep Analysis**: Analyze sleep data using GPT-4o and provide detailed analysis and scores.
- **API Documentation**: Swagger-based API documentation for easy integration.

## Basic Information  
- **Framework**: Python - Django  
- **Database**: MySQL (connected using DataSource with auto-pooling)  

## API Specification
The API documentation is available at:
[Swagger UI](https://sleep-diver.com/docs/swagger/)

## Deployment
The project includes a GitHub Actions workflow for CI/CD. The workflow is defined in .github/workflows/cicd.yml.  
- **docker-compose**
- **Dockerfile**
- **nginx**
- **gunicorn**
- **AWS EC2**
