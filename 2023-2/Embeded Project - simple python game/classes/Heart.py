import random

import numpy as np


class Heart:
    def __init__(self, block):
        self.position = np.array([block.position[0] + random.randint(0, block.length - 10), block.position[1] - 20])
        self.offset = block.position[0] - self.position[0]

    def re_positioning(self, block):
        self.position[1] = block.position[1] -20
        self.position[0] = block.position[0] - self.offset