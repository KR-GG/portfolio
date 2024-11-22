import numpy as np

def sigmoid(x):
    return 1 / (1 + np.exp(-x))

def sigmoid_derivative(x):
    return x * (1 - x)

class NeuralNetwork:
    def __init__(self, input_size, hidden1_size, hidden2_size, output_size):
        self.weights_input_hidden1 = np.random.rand(input_size, hidden1_size)
        self.weights_hidden1_hidden2 = np.random.rand(hidden1_size, hidden2_size)
        self.weights_hidden2_output = np.random.rand(hidden2_size, output_size)

        self.bias_hidden1 = np.zeros((1, hidden1_size))
        self.bias_hidden2 = np.zeros((1, hidden2_size))
        self.bias_output = np.zeros((1, output_size))

    def forward(self, input_data):
        self.hidden1_input = np.dot(input_data, self.weights_input_hidden1) + self.bias_hidden1
        self.hidden1_output = sigmoid(self.hidden1_input)
        self.hidden2_input = np.dot(self.hidden1_output, self.weights_hidden1_hidden2) + self.bias_hidden2
        self.hidden2_output = sigmoid(self.hidden2_input)
        self.output = np.dot(self.hidden2_output, self.weights_hidden2_output) + self.bias_output
        return self.output
    
    def backward(self, input_data, target, learning_factor):
        error = target - self.forward(input_data)
        d_output = error
        error_hidden2 = d_output.dot(self.weights_hidden2_output.T)
        d_hidden2 = error_hidden2 * sigmoid_derivative(self.hidden2_output)
        error_hidden1 = d_hidden2.dot(self.weights_hidden1_hidden2.T)
        d_hidden1 = error_hidden1 * sigmoid_derivative(self.hidden1_output)

        self.weights_hidden2_output += self.hidden2_output.T.dot(d_output) * learning_factor
        self.weights_hidden1_hidden2 += self.hidden1_output.T.dot(d_hidden2) * learning_factor
        self.weights_input_hidden1 += input_data.T.dot(d_hidden1) * learning_factor
        self.bias_output += np.sum(d_output, axis=0) * learning_factor
        self.bias_hidden2 += np.sum(d_hidden2, axis=0) * learning_factor
        self.bias_hidden1 += np.sum(d_hidden1, axis=0) * learning_factor

    def train(self, input_data, target, epochs, learning_factor):
        for _ in range(epochs):
            output = self.forward(input_data)
            self.backward(input_data, target, learning_factor)

def apply_threshold(x, threshold):
    if x > threshold:
        return 1
    else:
        return 0

print("ANN Network 구성을 시작합니다.")
while True:
    try:
        input_size = int(input("Set input_size: "))
        hidden1_size = int(input("Set hidden1_size: "))
        hidden2_size = int(input("Set hidden2_size: "))
        output_size = int(input("Set output_size: "))
        if input_size >= 0 and hidden1_size >= 0 and hidden2_size >= 0 and output_size >= 0:
            nn = NeuralNetwork(input_size, hidden1_size, hidden2_size, output_size)
            break
        else:
            print("입력값은 0 또는 양의 정수여야 합니다. 다시 입력하세요.")
    except ValueError:
        print("올바른 정수 값을 입력하세요.")

while True:
    mod = input("\nSelect MODE(0: set ANN, 1: trainning, 2: testing, 3: quit)\n")
    if mod == '0':
        while True:
            try:
                input_size = int(input("Set input_size: "))
                hidden1_size = int(input("Set hidden1_size: "))
                hidden2_size = int(input("Set hidden2_size: "))
                output_size = int(input("Set output_size: "))
                if input_size >= 0 and hidden1_size >= 0 and hidden2_size >= 0 and output_size >= 0:
                    nn = NeuralNetwork(input_size, hidden1_size, hidden2_size, output_size)
                    break
                else:
                    print("입력값은 0 또는 양의 정수여야 합니다. 다시 입력하세요.")
            except ValueError:
                print("올바른 정수 값을 입력하세요.")
        
    while mod=='1':
        print("\nTrainning을 시작합니다. Trainning의 종료를 원하시면 q를 입력해주세요")
        total_input_data = []
        total_output_data = []
        while True:
            input_data_string = input("Write input_data: ")
            if input_data_string == 'q':
                mod = '-1'
                break
            output_data_string = input("Write output_data: ")
            if output_data_string == 'q':
                mod = '-1'
                break

            if len(input_data_string) != input_size or len(output_data_string) != output_size:
                print("ANN 구조에 맞지 않는 입력값입니다. 다시 입력하세요.")
                continue
            elif not input_data_string.isdigit() or not output_data_string.isdigit():
                print("ANN 구조에 맞지 않는 입력값입니다. 다시 입력하세요.")
                continue
            elif set(input_data_string) - {'0', '1'} or set(output_data_string) - {'0', '1'}:
                print("입력값과 출력값은 0과 1로만 구성되어야 합니다. 다시 입력하세요.")
                continue

            input_data = []
            output_data = []

            for i in input_data_string:
                input_data.append(int(i))
            total_input_data.append(input_data)

            for i in output_data_string:
                output_data.append(int(i))
            total_output_data.append(output_data)

            loop = input("여러 개의 데이터를 한번에 학습시키려면 r을 입력하세요. 입력이 끝났다면 아무 키나 누르세요.\n")
            if loop == 'r' or loop == 'ㄱ': continue
            else: break

        total_input_data = np.array(total_input_data)
        total_output_data = np.array(total_output_data)

        if mod == '-1': break

        while True:
            try:
                epochs = input("Write epochs: ")
                if epochs == 'q':
                    mod = '-1'
                    break
                learning_factor = input("Write learning_factor: ")
                if learning_factor == 'q':
                    mod = '-1'
                    break

                epochs = int(epochs)
                learning_factor = float(learning_factor)

                if epochs <= 0 or learning_factor <= 0:
                    print("음수 또는 0 값은 허용되지 않습니다. 다시 입력하세요.")
                else:
                    break
            except ValueError:
                print("올바른 값을 입력하세요.")
        
        if mod=='-1': break

        try:
            nn.train(total_input_data, total_output_data, epochs, learning_factor)
        except ValueError:
            print("처음부터 다시 입력하세요.")
            continue

        predictions = nn.forward(total_input_data)
        print("Predictions:")
        print(predictions)

        while True:
            try:
                threshold = float(input("Set threshold: "))

                if 0 <= threshold <= 1:
                    break
                else:
                    print("입력값은 0과 1 사이의 실수여야 합니다. 다시 입력하세요.")
            except ValueError:
                print("올바른 실수 값을 입력하세요.")
        
        results = np.vectorize(apply_threshold)(predictions, threshold)
        print("Results:")
        print(results)

    while mod == '2':
        print("\nTesting을 시작합니다. Testing의 종료를 원하시면 q를 입력해주세요")
        total_input_for_testing = []
        while True:
            input_for_testing_string = input("Write input_data: ")
            if input_for_testing_string == 'q':
                mod = '-1'
                break

            if len(input_for_testing_string) != input_size:
                print("ANN 구조에 맞지 않는 입력값입니다. 다시 입력하세요.")
                continue
            elif not input_for_testing_string.isdigit():
                print("ANN 구조에 맞지 않는 입력값입니다. 다시 입력하세요.")
                continue
            elif set(input_for_testing_string) - {'0', '1'}:
                print("입력값과 출력값은 0과 1로만 구성되어야 합니다. 다시 입력하세요.")
                continue
            
            input_for_testing = []
            for i in input_for_testing_string:
                input_for_testing.append(int(i))
            total_input_for_testing.append(input_for_testing)

            loop = input("여러 개의 데이터를 한번에 테스트하려면 r을 입력하세요. 입력이 끝났다면 아무 키나 누르세요.\n")
            if loop == 'r' or loop == 'ㄱ': continue
            else: break
        
        if mod == '-1': break

        predictions = nn.forward(total_input_for_testing)
        print("Predictions:")
        print(predictions)

        while True:
            try:
                threshold = float(input("Set threshold: "))

                if 0 <= threshold <= 1:
                    break
                else:
                    print("입력값은 0과 1 사이의 실수여야 합니다. 다시 입력하세요.")
            except ValueError:
                print("올바른 실수 값을 입력하세요.")
        
        results = np.vectorize(apply_threshold)(predictions, threshold)
        print("Results:")
        print(results)

    if mod == '3':
        print("\n프로그램을 종료합니다")
        break