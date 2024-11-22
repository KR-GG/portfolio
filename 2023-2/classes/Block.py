import random

import numpy as np


class Block:
    def __init__(self, width, height):
        self.state = random.choice(['moving', 'fixed'])
        self.enemy = random.choice(['true', 'true', 'true','false', 'false'])
        self.heart = random.choice(['true', 'false', 'false', 'false', 'false'])
        self.icy = random.choice(['true', 'false'])
        self.v = 3.5
        self.length = random.randint(80, 120)
        start_position=random.randint(0, width-self.length)
        self.position = np.array([start_position, height-20])

    def moving(self, width):
        if self.state == 'moving':
            if self.position[0] + self.length >= width:
                self.v = -3.5
            elif self.position[0] <= 0:
                self.v = 3.5
            self.position[0] += self.v

    def move_up(self, t_up):
        if t_up:
            self.position[1] += 10

    def move_down(self, t_down):
        if t_down:
            self.position[1] -= 14