# SEED website project
**Domain**: [kau-seed.o-r.kr](http://kau-seed.o-r.kr)  

## Overview

The SEED website project is a web application that provides various services such as 공지사항, 행사 게시판, 아이디어 아카이브, 코드 자료실. The project is built using PHP and follows a RESTful API architecture.

### Key Directories and Files

- **.github/workflows**: Contains CI/CD configuration files.
- **docker-compose.yml**: Docker Compose configuration file.
- **Dockerfile**: Dockerfile for building the PHP-Apache container.
- **nginx.conf**: Nginx configuration file.
- **web/api**: Contains the API endpoints for various services.
- **web/includes**: Contains service classes and utility classes.
- **web/composer.json**: Composer configuration file for managing PHP dependencies.

## API Endpoints

### Board

- `GET /api/board/getAllBoards.php`: Retrieve all boards.
- `GET /api/board/getBoardById.php`: Retrieve a board by ID.
- `POST /api/board/createBoard.php`: Create a new board.
- `PUT /api/board/updateBoard.php`: Update an existing board.
- `DELETE /api/board/deleteBoard.php`: Delete a board.

### Event

- `GET /api/event/getAllEvents.php`: Retrieve all events.
- `GET /api/event/getEventById.php`: Retrieve an event by ID.
- `POST /api/event/createEvent.php`: Create a new event.
- `PUT /api/event/updateEvent.php`: Update an existing event.
- `DELETE /api/event/deleteEvent.php`: Delete an event.

### Idea

- `GET /api/idea/getAllIdeas.php`: Retrieve all ideas.
- `GET /api/idea/getIdeaById.php`: Retrieve an idea by ID.
- `POST /api/idea/createIdea.php`: Create a new idea.
- `PUT /api/idea/updateIdea.php`: Update an existing idea.

### Comment

- `GET /api/comment/getCommentsByIdeaId.php`: Retrieve comments by idea ID.
- `POST /api/comment/createComment.php`: Create a new comment.
- `PUT /api/comment/updateComment.php`: Update an existing comment.
- `DELETE /api/comment/deleteComment.php`: Delete a comment.

### Resource

- `GET /api/resource/getAllResources.php`: Retrieve all resources by category.
- `GET /api/resource/getResourceById.php`: Retrieve a resource by ID and category.
- `POST /api/resource/createResource.php`: Create a new resource.
- `PUT /api/resource/updateResource.php`: Update an existing resource.
- `DELETE /api/resource/deleteResource.php`: Delete a resource.
