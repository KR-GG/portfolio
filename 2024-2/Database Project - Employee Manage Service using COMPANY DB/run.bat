@echo off
setlocal

REM 사용자로부터 데이터베이스 이름, 사용자 이름, 비밀번호 입력 받기
set /p DB_NAME=Enter the database name:
set /p SPRING_DATASOURCE_USERNAME=Enter the database username:
set /p SPRING_DATASOURCE_PASSWORD=Enter the database password:

REM 환경 변수 설정
set SPRING_DATASOURCE_URL=jdbc:mysql://localhost:3306/%DB_NAME%
set SPRING_DATASOURCE_USERNAME=%SPRING_DATASOURCE_USERNAME%
set SPRING_DATASOURCE_PASSWORD=%SPRING_DATASOURCE_PASSWORD%

REM 환경 변수 출력 (디버깅용)
echo Database URL: %SPRING_DATASOURCE_URL%
echo Database Username: %SPRING_DATASOURCE_USERNAME%

REM Flyway clean and migrate
call mvnw flyway:clean flyway:migrate -Dflyway.url=%SPRING_DATASOURCE_URL% -Dflyway.user=%SPRING_DATASOURCE_USERNAME% -Dflyway.password=%SPRING_DATASOURCE_PASSWORD% -Dflyway.cleanDisabled=false

REM Maven clean install 및 run 실행
call mvnw clean install spring-boot:run -Dspring.datasource.url=%SPRING_DATASOURCE_URL% -Dspring.datasource.username=%SPRING_DATASOURCE_USERNAME% -Dspring.datasource.password=%SPRING_DATASOURCE_PASSWORD%

endlocal
pause