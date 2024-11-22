import numpy as np
import random

class ga_math:
    def __init__(self) -> None:
        self.vector_bits = 0
        self.selected_population = []
        self.evaluating_result = []
        self.start = 0
        self.finish = 0

    def initializing(self, start, finish):
        self.start = start
        self.finish = finish
        size = (finish - start) * (10 ** 6)
        self.vector_bits = size.bit_length()
        selected_rd_population = [random.randint(0, size) for _ in range(4)]
        for rd in selected_rd_population:
            self.selected_population.append(format(rd, f'0{self.vector_bits}b'))

    def evaluating(self, input_list, function):
        result_list = []
        for element in input_list:
            input = self.start + (self.finish-self.start) * int(element, 2) / (2 ** self.vector_bits)
            result_list.append(function(input))
        self.evaluating_result = result_list
    
    def mutation(self, parent):
        position = random.randint(0, self.vector_bits-1)
        mask = 1 << (self.vector_bits-1-position)
        result = format(int(parent, 2) ^ mask, f'0{self.vector_bits}b')
        return result
    
    def crossover(self, parent1, parent2):
        position = random.randint(0, self.vector_bits)
        result=''
        for i in parent1[:position+1]:
            result += i
        for i in parent2[position+1:]:
            result += i
        return result
    
    def sorting(self):
        a, b = zip(*sorted(zip(self.evaluating_result, self.selected_population), reverse=True))
        self.evaluating_result = list(a)
        self.selected_population = list(b)

    def evaluate_and_generate_n_times(self, n, f):
        self.sorting()
        for _ in range(n):
            self.selected_population[2] = self.mutation(self.selected_population[0])
            self.selected_population[3] = self.crossover(self.selected_population[0], self.selected_population[1])
            self.evaluating(self.selected_population, f)
            self.sorting()

def f(x):
    result = x * np.sin(10 * np.pi * x) + 1.
    return result

A = ga_math()
A.initializing(-1, 2)
A.evaluating(A.selected_population, f)
A.evaluate_and_generate_n_times(10000, f)
print(int(A.selected_population[0], 2), A.start + (A.finish-A.start) * int(A.selected_population[0], 2) / (2 ** A.vector_bits), A.evaluating_result[0])