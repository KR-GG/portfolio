import numpy as np


class Fire:
    def __init__(self, character):
        if character.my_ch_img == character.my_character:
            self.state = 'left'
            self.position = np.array([character.position[0]-15, character.position[1] + 2])
            self.v = -6
        else:
            self.state = 'right'
            self.position = np.array([character.position[0]+20, character.position[1] + 2])
            self.v = 6
    
    def move(self):
        self.position[0] += self.v

    def enemy_check(self, enemies):
        for index, enemy in enumerate(enemies):
            if enemy:
                head = self.position[0] if self.state == 'left' else self.position[0] + 15
                if(enemy.position[0] <= head <= enemy.position[0]+20 and enemy.position[1]-20 < self.position[1] < enemy.position[1]+20):
                    return index
        return -1
    
    def move_up(self, t_up):
        if t_up:
            self.position[1] += 10

    def move_down(self, t_down):
        if t_down:
            self.position[1] -= 14