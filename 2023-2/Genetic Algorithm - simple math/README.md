# GA Math

This project implements a simple genetic algorithm to optimize a mathematical function. The algorithm uses mutation, crossover, and selection to evolve a population of binary strings representing potential solutions.

## Requirements

- Python 3.x
- NumPy

## Usage

To run the genetic algorithm, execute the `GA_math.py` script:

```sh
python GA_math.py
```

## Code Overview

### `ga_math` Class

- `__init__(self)`: Initializes the class with default values.
- `initializing(self, start, finish)`: Initializes the population with random binary strings.
- `evaluating(self, input_list, function)`: Evaluates the population using the provided function.
- `mutation(self, parent)`: Performs mutation on a binary string.
- `crossover(self, parent1, parent2)`: Performs crossover between two binary strings.
- `sorting(self)`: Sorts the population based on their evaluation results.
- `evaluate_and_generate_n_times(self, n, f)`: Evaluates and generates new populations `n` times.

### `f(x)` Function

This function is defined as:
```python
def f(x):
    result = x * np.sin(10 * np.pi * x) + 1.
    return result
```
It is used as the objective function for the genetic algorithm.  
You can modify the function f(x) to apply the genetic algorithm to different mathematical functions.

## Example

The script initializes the population, evaluates it, and then evolves it over 10,000 generations. The best solution found is printed at the end.

```python
A = ga_math()
A.initializing(-1, 2)
A.evaluating(A.selected_population, f)
A.evaluate_and_generate_n_times(10000, f)
print(int(A.selected_population[0], 2), A.start + (A.finish-A.start) * int(A.selected_population[0], 2) / (2 ** A.vector_bits), A.evaluating_result[0])
```
