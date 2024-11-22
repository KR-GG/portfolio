server.py
 - python flask_socketio를 사용한 웹소켓 서버
 - 34.22.67.223:12345로 항상 열린 상태
 - 필요 패키지: flask, flask_socketio, flask_sqlalchemy, mysql
 - 새로운 서버를 열기 위해서는 sql 데이터 베이스 initial 과정이 따로 필요함

client.py
 - python을 이용하여 특정 ip주소:port의 웹 서버에 연결하고 응답을 받는 클라이언트
 - 소켓 connect을 initial하는 부분에 서버의 ip 주소와 포트 번호를 넣어주어야 함
 - 필요 패키지: socketio, request

쇼핑몰 서버
 - 메인 화면: Buy 또는 구매, F5 또는 새로고침, Quit 또는 종료를 입력하면 해당하는 기능을 수행
 - 물품 구매 화면(Buy 명령): 구매하려는 물품의 번호 혹은 이름을 입력하면 해당하는 물품을 구매