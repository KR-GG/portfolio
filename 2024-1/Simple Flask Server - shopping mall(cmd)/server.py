from flask import Flask, request, make_response
from flask_socketio import SocketIO, disconnect
from flask_sqlalchemy import SQLAlchemy

app = Flask(__name__)

# MySQL 데이터베이스 URI 설정
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://python:python@localhost/projectserver'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)
socketio = SocketIO(app)

class ServerState(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(50), nullable=False)
    price = db.Column(db.Integer, nullable=False)
    quantity = db.Column(db.Integer, nullable=False)

# 클라이언트별 상태 딕셔너리
clients_state = {}

def get_item(input_value):
    if input_value.isdigit():
        items = ServerState.query.all()
        item_number = int(input_value)
        if 1 <= item_number <= len(items):
            item = items[item_number - 1]
            return item
        else:
            return None
    else:
        item = ServerState.query.filter_by(name=input_value).first()
        if item:
            return item
        else:
            return None

def display_state(client_id=None):
    # 데이터베이스에서 서버의 데이터를 불러와 클라이언트에 특정 포맷으로 보냄
    states = ServerState.query.all()
    if states:
        line1 = ""
        line2 = ""
        line3 = ""
        line4 = ""
        line5 = ""
        row = ""
        count = 1
        for state in states:
            new_line1 = "_______________"
            new_line2 = f"{count}. {state.name}"
            new_line3 = f"{state.price} 원"
            new_line4 = f"{state.quantity} 개"
            new_line5 = "______________"
            line1 += new_line1
            line2 += "|" + new_line2.center(14 - len(state.name))
            line3 += "|" + new_line3.center(13)
            line4 += "|" + new_line4.center(13)
            line5 += "|" + new_line5
            count += 1
            if (count - 1) % 5 == 0 or state == states[-1]:
                line2 += "|"
                line3 += "|"
                line4 += "|"
                line5 += "|"
                row += line1 + "\n" + line2 + "\n" + line3 + "\n" + line4 + "\n" + line5 + "\n "
                line1 = ""
                line2 = ""
                line3 = ""
                line4 = ""
                line5 = ""
        row += "\n구매(Buy) | 새로고침(F5) | 종료(Quit)"
        if client_id:
            socketio.send(row, to=client_id)
        else:
            socketio.send(row)
    else:
        socketio.send('No server state available.', to=client_id)

def set_initial_data():
    initial_data = [
        {"name": "새우깡", "price": 1000, "quantity": 5},
        {"name": "메로나", "price": 500, "quantity": 10},
        {"name": "연필", "price": 700, "quantity": 43},
        {"name": "눈을감자", "price": 1700, "quantity": 3},
        {"name": "버터링", "price": 1300, "quantity": 5},
        {"name": "풍선껌", "price": 500, "quantity": 10},
    ]
    for item in initial_data:
        new_state = ServerState(name=item['name'], price=item['price'], quantity=item['quantity'])
        db.session.add(new_state)
    db.session.commit()

def buy_process(client_id):
    # 어떤 물품을 구매하고자 하는지 물어본 후 해당 번호의 물품을 구매하는 과정을 진행한다
    # 구매 중에 서버 데이터 베이스의 해당 물품의 수량을 업데이트하며
    # 구매를 중간에 취소할 경우 다시 물품의 수량을 복원한다
    # 구매의 중단은 'back'을 입력받으면 수행된다
    clients_state[client_id] = 'buying'
    socketio.send("어떤 상품을 구매하시겠습니까? | 뒤로가기(Back):", to=client_id)

def handle_buy_item(client_id, input_value):
    if input_value.lower() == 'back' or input_value == '뒤로가기':
        clients_state[client_id] = 'idle'
        display_state(client_id)
    else:
        item = get_item(input_value)
        if item:
            handle_purchase(client_id, item)
        else:
            socketio.send(f"'{input_value}'는 찾을 수 없습니다", to=client_id)

def handle_purchase(client_id, item):
    if item.quantity > 0:
        item.quantity -= 1
        db.session.commit()
        clients_state[client_id] = 'idle'
        socketio.send(f"{item.name}를 구매하였습니다", to=client_id)
        display_state(client_id)
    else:
        socketio.send(f"{item.name}의 재고가 없습니다", to=client_id)


@socketio.on('connect')
def handle_connect():
    client_id = request.sid
    clients_state[client_id] = 'idle'
    print(f'Client {client_id} connected')
    display_state(client_id)

@socketio.on('disconnect')
def handle_disconnect():
    client_id = request.sid
    clients_state.pop(client_id, None)
    print('Client disconnected')

@socketio.on('message')
def handle_message(message):
    # 클라이언트로부터 온 해당 메시지(message)에 대해 역할을 수행함
    client_id = request.sid
    if clients_state.get(client_id) == 'buying':
        handle_buy_item(client_id, message)
    else:
        if message.lower() == 'f5' or message == '새로고침':
            display_state(client_id)
        elif message.lower() == 'quit' or message == '종료':
            disconnect()
        elif message.lower() == 'buy' or message == '구매':
            buy_process(client_id)
        else:
            # 디버깅용
            socketio.send(f'Received message: {message}', to=client_id)


if __name__ == '__main__':
    with app.app_context():
        db.drop_all()
        db.create_all()  # 데이터베이스와 테이블을 생성합니다.
        if not ServerState.query.first():
            set_initial_data()
    socketio.run(app, host='0.0.0.0', port=12345)
