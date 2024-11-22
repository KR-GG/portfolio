import numpy as np


class Character:
    def __init__(self):
        self.state = 'ground'
        self.vertical_speed = 0
        self.horizontal_speed = 0
        self.position = np.array([110, 200])
        self.opp = True

    def move(self, ground, command = None):
        if command['jump'] and self.vertical_speed == 0:
            self.vertical_speed = -16
            self.horizontal_speed = 0

        self.position[1] += self.vertical_speed

        if ground.icy == 'true':
            if self.horizontal_speed:
                self.horizontal_speed += 1 if self.horizontal_speed<0 else -1
            if ground.state == 'moving' and self.state == 'ground':
                    self.horizontal_speed += ground.v
            if command['left_pressed']:
                self.horizontal_speed -= 7
            if command['right_pressed']:
                self.horizontal_speed += 7
            self.position[0] += self.horizontal_speed
            if command['left_pressed']:
                self.horizontal_speed += 5
            if command['right_pressed']:
                self.horizontal_speed -= 5
            if ground.state == 'moving' and self.state == 'ground':
                self.horizontal_speed -= ground.v
        
        else:
            self.horizontal_speed = 0
            if ground.state == 'moving' and self.state == 'ground':
                self.horizontal_speed += ground.v
            if command['left_pressed']:
                self.horizontal_speed -= 5
            if command['right_pressed']:
                self.horizontal_speed += 5
            self.position[0] += self.horizontal_speed
        
    def re_positioning(self, ground):
        if (self.position[1]+20 > ground.position[1]): 
            self.position[1] = ground.position[1] - 20

    def ground_check(self, ground):
        on_ground = self.on_ground(ground)
        
        if on_ground:
            self.state = 'ground'
            self.re_positioning(ground)
            self.vertical_speed = 0
        
        else:
            if self.state == 'fly':
                self.vertical_speed += 2.5
            else: self.state = 'fly'
    
    def next_block_check(self, next_block):
        on_next = self.on_ground(next_block) and self.vertical_speed >= 0

        if on_next:
            self.re_positioning(next_block)
            self.state = 'ground'
            self.vertical_speed = 0
            return 1
        return 0
        
    def falling_check(self, ground):
        falling = self.position[1]+20 >= ground.position[1] + 20

        return falling

    def on_ground(self, ground):
        return ground.position[1] <= self.position[1]+20 <= ground.position[1]+20 and ground.position[0] <= self.position[0]+10 <= ground.position[0] + ground.length
    
    def move_up(self, t_up, bg_start):
        if t_up:
            self.position[1] += 10
            return bg_start + 10
        return bg_start

    def move_down(self, t_down, bg_start):
        if t_down:
            self.position[1] -= 14
            return bg_start -14
        return bg_start

    def enemy_check(self, enemies):
        for index, enemy in enumerate(enemies):
            if enemy:
                if (enemy.position[0]-10 <= self.position[0] <= enemy.position[0]+10 and enemy.position[1]-20 <= self.position[1] <= enemy.position[1]+20) or (enemy.position[0]-20 <= self.position[0] <= enemy.position[0]+20 and enemy.position[1]-10 <= self.position[1] <= enemy.position[1]+10):
                    return index
        return -1
    
    def heart_check(self, hearts):
        for index, heart in enumerate(hearts):
            if heart:
                if (heart.position[0]-20 <= self.position[0] <= heart.position[0] + 15 and heart.position[1] - 20 <= self.position[1] <= heart.position[1] + 15):
                    return index
        return -1