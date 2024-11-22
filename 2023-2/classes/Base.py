import numpy as np


class Base:
    def __init__(self):
        self.state = 'fixed'
        self.enemy = 'false'
        self.icy = 'false'
        self.v = 0
        self.length = 240
        self.position = np.array([0, 220])

    def moving(self, width):
        pass

    def move_up(self, t_up):
        if t_up:
            self.position[1] += 10

    def move_down(self, t_down):
        if t_down:
            self.position[1] -= 14