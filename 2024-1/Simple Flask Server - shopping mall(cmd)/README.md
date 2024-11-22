# 쇼핑몰 서버 프로젝트

## 개요
이 프로젝트는 컴퓨터 네트워크 TCP 통신을 학습하고 분석해보기 위한 간단한 프로젝트로 Python을 사용하여 구현된 쇼핑몰 서버와 클라이언트입니다. 서버는 Flask와 Flask-SocketIO를 사용하여 웹소켓 통신을 처리하며, MySQL 데이터베이스를 사용하여 상품 정보를 관리합니다. 클라이언트는 SocketIO를 사용하여 서버와 통신합니다.

## 서버 (`server.py`)
- **기능**: Python Flask-SocketIO를 사용한 웹소켓 서버
- **주소**: `http://34.22.67.223:12345`로 항상 열린 상태
- **필요 패키지**: `flask`, `flask_socketio`, `flask_sqlalchemy`, `mysql`
- **설정**: 새로운 서버를 열기 위해서는 SQL 데이터베이스 초기화 과정이 필요함

### 주요 함수
- `get_item(input_value)`: 입력된 값에 따라 상품을 조회
- `display_state(client_id=None)`: 서버의 현재 상태를 특정 포맷으로 클라이언트에 전송
- `set_initial_data()`: 초기 데이터를 데이터베이스에 설정
- `buy_process(client_id)`: 구매 프로세스를 시작
- `handle_buy_item(client_id, input_value)`: 구매할 상품을 처리
- `handle_purchase(client_id, item)`: 실제 구매를 처리

### 이벤트 핸들러
- `@socketio.on('connect')`: 클라이언트 연결 처리
- `@socketio.on('disconnect')`: 클라이언트 연결 해제 처리
- `@socketio.on('message')`: 클라이언트로부터의 메시지 처리

## 클라이언트 (`client.py`)
- **기능**: 특정 IP 주소와 포트의 웹 서버에 연결하고 응답을 받는 클라이언트
- **필요 패키지**: `socketio`, `request`

### 주요 함수
- `send_messages()`: 사용자 입력을 서버로 전송
- `on_connect()`: 서버에 연결되었을 때 호출
- `on_message(message)`: 서버로부터 메시지를 받았을 때 호출

## 사용 방법
1. 필요한 패키지를 설치합니다:
    ```sh
    pip install flask flask_socketio flask_sqlalchemy mysql socketio request
    ```
2. 서버를 실행합니다:
    ```sh
    python server.py
    ```
3. 클라이언트를 실행합니다:
    ```sh
    python client.py
    ```

## 주요 명령어
- **메인 화면**: `Buy` 또는 `구매`, `F5` 또는 `새로고침`, `Quit` 또는 `종료`를 입력하면 해당하는 기능을 수행
- **물품 구매 화면**: 구매하려는 물품의 번호 혹은 이름을 입력하면 해당하는 물품을 구매
