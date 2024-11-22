# ANN Network using sigmoid

This program implements an Artificial Neural Network (ANN) using the sigmoid activation function. Users can configure the network structure, input training data to train the network, and input test data to check the prediction results.

## Requirements
- Python 3.x
- NumPy

## Usage

1. Run the program:
    ```sh
    python ANN_Network_sigmoid.py
    ```

2. Configure the network structure:
    - `input_size`: Number of nodes in the input layer
    - `hidden1_size`: Number of nodes in the first hidden layer
    - `hidden2_size`: Number of nodes in the second hidden layer
    - `output_size`: Number of nodes in the output layer

3. Select a mode:
    - `0`: Configure the network
    - `1`: Train the network
    - `2`: Test the network
    - `3`: Exit the program

### Training the Network

1. Select training mode (`1`).
2. Input the training data:
    - `input_data`: Input data (string of 0s and 1s)
    - `output_data`: Output data (string of 0s and 1s)
3. Set the number of epochs and the learning rate.
4. After training, you can check the prediction results.

### Testing the Network

1. Select testing mode (`2`).
2. Input the test data:
    - `input_data`: Input data (string of 0s and 1s)
3. Set the threshold to binarize the prediction results.
4. Check the test results.

## Main Functions and Classes

- `sigmoid(x)`: Sigmoid activation function
- `sigmoid_derivative(x)`: Derivative of the sigmoid function
- `apply_threshold(x, threshold)`: Function to apply a threshold for binarization
- `NeuralNetwork`: Neural network class
  - `__init__(self, input_size, hidden1_size, hidden2_size, output_size)`: Initialize the neural network
  - `forward(self, input_data)`: Forward propagation
  - `backward(self, input_data, target, learning_factor)`: Backward propagation and weight update
  - `train(self, input_data, target, epochs, learning_factor)`: Train the neural network

## Example

Here is an example of running the program:

```sh
Starting ANN Network configuration.
Set input_size: 3
Set hidden1_size: 4
Set hidden2_size: 4
Set output_size: 1

Select MODE(0: set ANN, 1: training, 2: testing, 3: quit)
1

Starting training. To stop training, enter q.
Write input_data: 101
Write output_data: 1
Write input_data: q

Write epochs: 1000
Write learning_factor: 0.1

Predictions:
[[0.73105858]]

Set threshold: 0.5
Results:
[[1]]
