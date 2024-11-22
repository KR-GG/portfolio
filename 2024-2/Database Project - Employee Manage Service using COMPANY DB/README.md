# KAU-2024-DB Project

## Basic Information  
- **Framework**: Java - Spring Boot  
- **Database**: MySQL (connected using DataSource with auto-pooling)  
- **Dependency Management**: Maven  
- **Architecture**: Separate DAO and Service Interfaces  

## API Specification
The API documentation is available at:
[Swagger UI](http://13.209.190.181:8080/swagger-ui/index.html#/)

## Setup Instructions

### Prerequisites
- Java 11 or higher
- Maven 3.6.3 or higher
- Docker (for containerized deployment)

### Local Setup

1. Clone the repository:
    ```sh
    git clone https://github.com/2024-DB-project/Back-end
    ```

2. Navigate to the `Back-end` directory:
    ```sh
    cd Back-end
    ```
    **Note**: Ensure that the path does not contain any Korean characters as it may cause `ClassNotFound` errors during the Maven build process.

3. Run the appropriate script for your environment:
    - For Windows:
        ```sh
        run.bat
        ```
    - For UNIX:
        ```sh
        ./run.sh
        ```

4. Enter your local MySQL URL, username, and password when prompted.

5. Once the build and run process is complete, access the application at:
    ```
    http://localhost:8080
    ```

6. If running the batch file is not possible, execute the following commands in the program folder:
    ```sh
    ./mvnw flyway:clean flyway:migrate -Dflyway.url={your_database_url} -Dflyway.user={your_database_username} -Dflyway.password={your_database_password} -Dflyway.cleanDisabled=false
    ./mvnw clean install spring-boot:run -Dspring.datasource.url={your_database_url} -Dspring.datasource.username={your_database_username} -Dspring.datasource.password={your_database_password}
    ```
    Replace the placeholders with your actual database details:
    - `{your_database_url}`: e.g., [jdbc:mysql://localhost:3306/mydb](http://_vscodecontentref_/9)
    - `{your_database_username}`: e.g., `admin`
    - `{your_database_password}`: Your database user password

### Deployment
The project uses GitHub Actions for deployment. The deployment configuration can be found in deploy.yml.

### Database Migration
Database migrations are managed using Flyway. Migration scripts are located in the [migration](https://github.com/KR-GG/portfolio/tree/master/2024-2/Database%20Project%20-%20Employee%20Manage%20Service%20using%20COMPANY%20DB/src/main/resources/db/migration) directory.

### Swagger Configuration
Swagger is configured in the SwaggerConfig.java file. The OpenAPI specification is loaded from [openapi.json](https://github.com/KR-GG/portfolio/blob/master/2024-2/Database%20Project%20-%20Employee%20Manage%20Service%20using%20COMPANY%20DB/src/main/resources/static/openapi.json).
