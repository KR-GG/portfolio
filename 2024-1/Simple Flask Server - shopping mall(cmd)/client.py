import socketio
import threading
import sys

sio = socketio.Client()

def send_messages():
    while True:
        message = input()
        sio.send(message)

@sio.on('connect')
def on_connect():
    threading.Thread(target=send_messages, daemon=True).start()

@sio.on('message')
def on_message(message):
    print(f'\nReceived message from server:\n{message}\n')

if __name__ == '__main__':
    try:
        sio.connect('http://34.22.67.223:12345')
        sio.wait()
    except KeyboardInterrupt:
        sys.exit()
