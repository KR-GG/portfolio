#!/bin/bash

# 사용자로부터 입력 받기
read -p "Enter the database name: " DB_NAME
read -p "Enter the database username: " SPRING_DATASOURCE_USERNAME
read -sp "Enter the database password: " SPRING_DATASOURCE_PASSWORD
echo

# 환경 변수 설정
export SPRING_DATASOURCE_URL="jdbc:mysql://localhost:3306/$DB_NAME"
export SPRING_DATASOURCE_USERNAME=$SPRING_DATASOURCE_USERNAME
export SPRING_DATASOURCE_PASSWORD=$SPRING_DATASOURCE_PASSWORD

# 환경 변수 출력 (디버깅용)
echo "Database URL: $SPRING_DATASOURCE_URL"
echo "Database Username: $SPRING_DATASOURCE_USERNAME"

# Flyway clean 및 migrate 실행
./mvnw flyway:clean flyway:migrate -Dflyway.url=$SPRING_DATASOURCE_URL -Dflyway.user=$SPRING_DATASOURCE_USERNAME -Dflyway.password=$SPRING_DATASOURCE_PASSWORD -Dflyway.cleanDisabled=false

# Maven clean install 및 run 실행
./mvnw clean install spring-boot:run -Dspring.datasource.url=$SPRING_DATASOURCE_URL -Dspring.datasource.username=$SPRING_DATASOURCE_USERNAME -Dspring.datasource.password=$SPRING_DATASOURCE_PASSWORD