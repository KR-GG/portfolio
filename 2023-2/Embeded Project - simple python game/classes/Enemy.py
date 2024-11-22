import random
import numpy as np


class Enemy:
    def __init__(self, block):
        self.position = np.array([block.position[0] + random.randint(0, block.length - 10), block.position[1] - 25])
        self.v = 2

    def re_positioning(self, block):
        self.position[1] = block.position[1] -25
        if self.position[0] >= block.position[0] + block.length - 10:
            self.v = -2
        elif self.position[0] <= block.position[0] + 10:
            self.v = 2
        self.position[0] += self.v
