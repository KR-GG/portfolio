from colorsys import hsv_to_rgb
import board
from digitalio import DigitalInOut, Direction
from PIL import Image, ImageDraw, ImageFont
from adafruit_rgb_display import st7789
import numpy as np

import classes.Character as Character
import classes.Base as Base
import classes.Block as Block
import classes.Heart as Heart
import classes.Enemy as Enemy
import classes.Fire as Fire

class Joystick:
    def __init__(self):
        self.cs_pin = DigitalInOut(board.CE0)
        self.dc_pin = DigitalInOut(board.D25)
        self.reset_pin = DigitalInOut(board.D24)
        self.BAUDRATE = 24000000

        self.spi = board.SPI()
        self.disp = st7789.ST7789(
                    self.spi,
                    height=240,
                    y_offset=80,
                    rotation=180,
                    cs=self.cs_pin,
                    dc=self.dc_pin,
                    rst=self.reset_pin,
                    baudrate=self.BAUDRATE,
                    )

        # Input pins:
        self.button_A = DigitalInOut(board.D5)
        self.button_A.direction = Direction.INPUT

        self.button_B = DigitalInOut(board.D6)
        self.button_B.direction = Direction.INPUT

        self.button_L = DigitalInOut(board.D27)
        self.button_L.direction = Direction.INPUT

        self.button_R = DigitalInOut(board.D23)
        self.button_R.direction = Direction.INPUT

        self.button_U = DigitalInOut(board.D17)
        self.button_U.direction = Direction.INPUT

        self.button_D = DigitalInOut(board.D22)
        self.button_D.direction = Direction.INPUT

        self.button_C = DigitalInOut(board.D4)
        self.button_C.direction = Direction.INPUT

        # Turn on the Backlight
        self.backlight = DigitalInOut(board.D26)
        self.backlight.switch_to_output()
        self.backlight.value = True

        # Create blank image for drawing.
        # Make sure to create image with mode 'RGB' for color.
        self.width = self.disp.width
        self.height = self.disp.height

joystick = Joystick()

font = ImageFont.truetype("./font/HedvigLettersSans-Regular.ttf", 15)
font_small = ImageFont.truetype("./font/HedvigLettersSans-Regular.ttf", 12)
font_title_60 = ImageFont.truetype("./font/BlakaInk-Regular.ttf", 60)
font_title_45 = ImageFont.truetype("./font/BlakaInk-Regular.ttf", 45)
font_title_40 = ImageFont.truetype("./font/BlakaInk-Regular.ttf", 40)
font_title_25 = ImageFont.truetype("./font/BlakaInk-Regular.ttf", 25)

bg_image = Image.new("RGB", (joystick.width, joystick.height))
my_character = Image.open('./res/pink_ch.png')
my_character_opp = Image.open('./res/pink_ch_opp.png')
base_image = Image.open('./res/ground_240_20.png')
block_image = Image.open('./res/ground_120_20.png')
icy_image = Image.open('./res/ground_120_20_icy.png')
enemy_image = Image.open('./res/enemy_img.png')
enemy_opp_image = Image.open('./res/enemy_opp_img.png')
cloud_image = Image.open('./res/cloud_img.png')
fire_image = Image.open('./res/fire.png')
fire_opp_image = Image.open('./res/fire_opp.png')
heart_image = Image.open('./res/heart.png')
rule_image = Image.open('./res/rule.png')

bg_draw = ImageDraw.Draw(bg_image)

def create_gauge_bar(cooltime):
    if cooltime == 0: return 0
    image = Image.new("RGB", (25, 4), "white")
    draw = ImageDraw.Draw(image)

    draw.rectangle((0,0,25,4), outline="white")

    fill = int((50-cooltime) / 2)
    draw.rectangle((0,0,fill,4), fill=(255,102,0))
    return image

records = []
record = 0
last_score = 0
start_page = 3
end_page = 0
delay = 0
my_ch = Character()
my_base = Base()
blocks = [] # height: 380 / 310 / 240 / 170 / 100 / 30
enemies = [0, 0, 0, 0, 0, 0]
hearts = [0, 0, 0, 0, 0, 0]
fireball = 0
cooltime = 0
bg_start = 0
health = 3
stair = 0
score = 0
current_index = 2
next_index = 3
t_up = 0
t_down = 0
fall_stack = 0
spawn_h = 380
while spawn_h >= 30:
    blocks.append(Block(joystick.width, spawn_h))
    spawn_h -= 70
blocks[2] = my_base
for index, block in enumerate(blocks):
    if block.state == 'fixed' and block.enemy =='true':
        enemies[index] = Enemy(block)
    if block.state == 'moving' and block.enemy =='false' and block.heart == 'true':
        hearts[index] = Heart(block)

while True:
    delay -= 1 if delay else 0
    cooltime -= 1 if cooltime else 0
    command = {'move': False, 'jump': False, 'attack': False, 'left_pressed': False, 'right_pressed': False}
    
    if not joystick.button_A.value: # A pressed
        command['jump'] = True

    if not joystick.button_L.value:  # left pressed
        command['left_pressed'] = True
        command['move'] = True

    if not joystick.button_R.value:  # right pressed
        command['right_pressed'] = True
        command['move'] = True

    if not joystick.button_B.value and cooltime == 0: # B pressed
        command['attack'] = True
        try:
            fireball = Fire(my_ch)
            cooltime += 50
        except NameError:
            pass

    if (start_page == 0 and end_page == 0):
        for block in blocks:
            block.moving(joystick.width)

        up = my_ch.next_block_check(blocks[next_index])
        if up:
            if fall_stack:
                current_index += 1
                next_index += 1
                fall_stack -= 1
            else:
                score += 10
                blocks = blocks[1:]
                blocks.append(Block(joystick.width, blocks[-1].position[1]-50))
                enemies = enemies[1:]
                if blocks[-1].enemy == 'true' and blocks[-1].state == 'fixed':
                    enemies.append(Enemy(blocks[-1]))
                else:
                    enemies.append(0)
                hearts = hearts[1:]
                if blocks[-1].state == 'moving' and blocks[-1].enemy == 'false' and blocks[-1].heart == 'true':
                    hearts.append(Heart(blocks[-1]))
                else:
                    hearts.append(0)
            t_up += 7 if stair else 5
            stair += 1
        
        bg_start = my_ch.move_up(t_up, bg_start)
        for block in blocks:
            block.move_up(t_up)
        if fireball: fireball.move_up(t_up)
        t_up -= 1 if t_up else 0

        fall = my_ch.falling_check(blocks[current_index])
        if fall:
            if stair == 0: fall_stack = 3
            elif stair == 1:
                fall_stack += 1
                stair = 0
                current_index -= 1
                next_index -= 1
                my_ch.position[1] -= 8
                for block in blocks:
                    block.position[1] -= 8
                t_down += 3
            else:
                fall_stack += 1
                stair -= 1
                current_index -= 1
                next_index -= 1
                t_down += 5

        if fall_stack > 2:
            delay = 10
            record = score
            last_score = record
            start_page = 1
            end_page = 1
            my_ch = Character()
            my_base = Base()
            blocks = [] # height: 380 / 310 / 240 / 170 / 100 / 30
            enemies = [0, 0, 0, 0, 0, 0]
            hearts = [0, 0, 0, 0, 0, 0]
            fireball = 0
            bg_start = 0
            health = 3
            stair = 0
            score = 0
            current_index = 2
            next_index = 3
            t_up = 0
            t_down = 0
            fall_stack = 0
            spawn_h = 380
            while spawn_h >= 30:
                blocks.append(Block(joystick.width, spawn_h))
                spawn_h -= 70
            blocks[2] = my_base
            for index, block in enumerate(blocks):
                if block.state == 'fixed' and block.enemy =='true':
                    enemies[index] = Enemy(block)
                if block.state == 'moving' and block.enemy =='false' and block.heart == 'true':
                    hearts[index] = Heart(block)
            continue

        bg_start = my_ch.move_down(t_down, bg_start)
        for block in blocks:
            block.move_down(t_down)
        if fireball: fireball.move_down(t_down)
        t_down -= 1 if t_down else 0
    
        my_ch.ground_check(blocks[current_index])
        my_ch.move(blocks[current_index], command)

        if fireball:
            fireball.move()
            hit = fireball.enemy_check(enemies)
            if hit != -1:
                score += 10
                enemies[hit] = 0
            head = fireball.position[0] if fireball.state == 'left' else fireball.position[0] + 15
            if -15 > head or joystick.width < head:
                fireball = 0

        for index, enemy in enumerate(enemies):
            if enemy:
                enemy.re_positioning(blocks[index])
        for index, heart in enumerate(hearts):
            if heart:
                heart.re_positioning(blocks[index])

        hit = my_ch.enemy_check(enemies)
        if hit != -1:
            health -= 1
            enemies[hit] = 0

        if health == 0:
            delay = 10
            record = score
            last_score = record
            start_page = 1
            end_page = 1
            my_ch = Character()
            my_base = Base()
            blocks = [] # height: 380 / 310 / 240 / 170 / 100 / 30
            enemies = [0, 0, 0, 0, 0, 0]
            hearts = [0, 0, 0, 0, 0, 0]
            fireball = 0
            bg_start = 0
            health = 3
            stair = 0
            score = 0
            current_index = 2
            next_index = 3
            t_up = 0
            t_down = 0
            fall_stack = 0
            spawn_h = 380
            while spawn_h >= 30:
                blocks.append(Block(joystick.width, spawn_h))
                spawn_h -= 70
            blocks[2] = my_base
            for index, block in enumerate(blocks):
                if block.state == 'fixed' and block.enemy =='true':
                    enemies[index] = Enemy(block)
                if block.state == 'moving' and block.enemy =='false' and block.heart == 'true':
                    hearts[index] = Heart(block)
            continue

        hit = my_ch.heart_check(hearts)
        if hit != -1:
            health += 1
            hearts[hit] = 0

        #그리는 순서가 중요합니다. 배경을 먼저 깔고 위에 그림을 그리고 싶었는데 그림을 그려놓고 배경으로 덮는 결과로 될 수 있습니다.
        bg_draw.rectangle((0, 0, joystick.width, joystick.height), fill = (255, 255, 255, 100))
        temp = bg_start
        while temp > -240:
            bg_image.paste(cloud_image, (0, temp), cloud_image)
            temp -= 240
        for block in blocks:
            if block == my_base: pass
            if block.icy == 'true':
                bg_image.paste(icy_image.resize(tuple(map(int, (block.length, 20)))), tuple(block.position))
            else:
                bg_image.paste(block_image.resize(tuple(map(int,(block.length, 20)))), tuple(block.position))
        if stair == 0: bg_image.paste(base_image, tuple(my_base.position))
        for enemy in enemies:
            if enemy:
                enemy_img = enemy_image if enemy.v > 0 else enemy_opp_image
                bg_image.paste(enemy_img, tuple(enemy.position), enemy_img)
        for heart in hearts:
            if heart:
                bg_image.paste(heart_image, tuple(heart.position), heart_image)
        bg_draw.text((3, 2), f"SCORE {score}", font = font, fill = (0,0,0,100))
        bg_draw.text((3, 18), f"LIFE", font = font, fill = (0,0,0,100))
        temp = health
        while temp > 0:
            heart_position = 18 + temp*20
            bg_image.paste(heart_image, (heart_position, 20), heart_image)
            temp -= 1
        try:
            my_ch_img
        except NameError: 
            my_ch_img = my_character
        if command['left_pressed'] == True: my_ch.opp = True; my_ch_img = my_character
        if command['right_pressed'] == True: my_ch.opp = False; my_ch_img = my_character_opp
        bg_image.paste(my_ch_img, tuple(my_ch.position), my_ch_img)
        if fireball:
            if fireball.state == 'left':
                bg_image.paste(fire_opp_image, tuple(fireball.position), fire_opp_image)
            else:
                bg_image.paste(fire_image, tuple(fireball.position), fire_image)
        if cooltime:
            bar = create_gauge_bar(cooltime)
            if bar:
                bg_image.paste(bar, (my_ch.position[0]-2, my_ch.position[1]-8))

    if record:
        try:
            records[4]
        except IndexError:
            records.append(record)
        else:
            if record > records[4]:
                records[4] = record
        record = 0
        records.sort(reverse=True)

    if command['right_pressed'] == True and delay == 0:
        if end_page:
            end_page = 0
            delay += 5
        elif start_page:
            start_page -= 1
            delay += 5

    if start_page == 1:
        bg_image.paste(cloud_image, (0, 0), cloud_image)
        bg_image.paste(base_image, tuple(my_base.position))
        bg_draw.text((55, 50), "PONG!", font = font_title_60, fill=(203,153,000))
        bg_draw.text((45, 150), "Press -> to start GAME", font = font, fill=(0,0,0))

    if start_page == 2:
        bg_image.paste(rule_image, (0,0), rule_image)
        bg_draw.text((28,22), "HOW TO PLAY", font = font_title_40, fill = (204,102,0))
        bg_draw.text((33,69), "Jump to the next block!", font = font_small, fill = (0,0,0))
        bg_draw.text((40,82), "jump:#5     attack:#6", font = font_small, fill = (0,0,0))
        bg_draw.text((33,98), "Stay away               Take hearts", font = font_small, fill = (0,0,0))
        bg_draw.text((33,111), "from enemies!      and keep alive", font = font_small, fill = (0,0,0))

    if start_page == 3:
        bg_image.paste(cloud_image, (0, 0), cloud_image)
        bg_image.paste(base_image, tuple(my_base.position))
        bg_draw.text((55, 50), "PONG!", font = font_title_60, fill=(203,153,000))
        bg_draw.text((45, 150), "Press -> to start GAME", font = font, fill=(0,0,0))

    if end_page == 1:
        bg_draw.rectangle((0, 0, joystick.width, joystick.height), fill = (10,10,10, 30))
        bg_draw.text((20,5), "GAME OVER", font = font_title_45, fill=(153,000,51))
        bg_draw.text((20,210), "My SCORE", font = font_title_25, fill=(255,204,000))
        bg_draw.text((140,210), f"{last_score}", font = font_title_25, fill=(255,204,000))
        bg_draw.text((20,60), "1st", font = font_title_25)
        bg_draw.text((20,90), "2nd", font = font_title_25)
        bg_draw.text((20,120), "3rd", font = font_title_25)
        bg_draw.text((20,150), "4th", font = font_title_25)
        bg_draw.text((20,180), "5th", font = font_title_25)

        try:
            bg_draw.text((140,60), f"{records[0]}", font = font_title_25)
        except IndexError:
            pass
        try:
            bg_draw.text((140,90), f"{records[1]}", font = font_title_25)
        except IndexError:
            pass
        try:
            bg_draw.text((140,120), f"{records[2]}", font = font_title_25)
        except IndexError:
            pass
        try:
            bg_draw.text((140,150), f"{records[3]}", font = font_title_25)
        except IndexError:
            pass
        try:
            bg_draw.text((140,180), f"{records[4]}", font = font_title_25)
        except IndexError:
            pass

    joystick.disp.image(bg_image)