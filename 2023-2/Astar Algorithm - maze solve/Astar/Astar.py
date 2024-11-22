from tkinter import *
import sys
sys.path.append('../')
import execution
from functools import partial
from time import sleep


def center_gui(root):

    windowWidth = root.winfo_reqwidth()
    windowHeight = root.winfo_reqheight()

    positionRight = int(root.winfo_screenwidth()/2 - windowWidth/2)
    positionDown = int(root.winfo_screenheight()/2 - windowHeight/2)

    root.geometry("+{}+{}".format(positionRight, positionDown))


def pop_up_window(app):
    """
    Displays the start up window with the instructions.
    When the pop up is displayed the buttons at the backround
    are disabled
    """

    def start_button_action():

        app.enable_buttons()
        win.destroy()

    win = Toplevel()
    win.wm_title("환영합니다!")

    Label(win, text="1단계 : 시작점을 선택하세요.",
          font=("Calibri", 13), pady=5, padx=10).pack()
    Label(win, text="2단계 : 도착점을 선택하세요.", font=(
        "Calibri", 13), pady=5, padx=10).pack()
    Label(win, text="3단계 : 장애물을 선택하세요.", font=(
        "Calibri", 13), pady=5, padx=10).pack()
    Label(win, text="클릭하고 마우스를 움직이고 다시 클릭하면 장애물이 선택됩니다.", padx=25).pack()
    Label(win, text="4단계 : Enter를 누르세요.",
          font=("Calibri", 13), pady=5, padx=10).pack()
    Label(win, text="5단계 : 다시 시작하려면 R을 누르세요.",
          font=("Calibri", 13), pady=5, padx=10).pack()
    Button(win, text="Start", command=start_button_action,
           ).pack()

    win.update_idletasks()
    center_gui(win)


class App:

    def __init__(self, master):

        self.master = master
        master.wm_title("A* Algorithm")
        self.buttons = []
        self.start = []
        self.goal = []
        self.obstacles = []
        self.mode = 0

        for i in range(25):
            self.buttons.append([])
            for j in range(25):

                button = Button(master, width=2, height=1,
                                command=partial(self.button_operation, i, j), state="disabled")

                self.buttons[i].append(button)

                self.buttons[i][j].bind('<Enter>', partial(
                    self.add_obstacle, i, j))

                self.buttons[i][j].grid(row=i, column=j)

        master.update_idletasks()
        center_gui(master)

        pop_up_window(self)

    def enable_buttons(self):

        for i in range(25):
            for j in range(25):
                self.buttons[i][j].configure(state="normal")

    def disable_buttons(self):

        for i in range(25):
            for j in range(25):
                self.buttons[i][j].configure(state="disable")

    def button_operation(self, row, column):

        if self.mode == 0:

            self.start.append(row)
            self.start.append(column)
            self.mode = 1
            self.buttons[row][column].configure(bg='green')

        elif self.mode == 1:

            self.goal.append(row)
            self.goal.append(column)
            self.mode = 2
            self.buttons[row][column].configure(bg='red')

        elif self.mode == 2:
            self.mode = 3

        else:
            self.mode = 2

    def add_obstacle(self, row, column, event):
        if self.mode == 3:
            obstacle_node = []
            obstacle_node.append(row)
            obstacle_node.append(column)

            self.obstacles.append(obstacle_node[:])
            self.buttons[row][column].configure(bg='black')

    def heuristic(self, node1, node2):
        result = abs(node1[0] - node2[0]) + abs(node1[1]-node2[1])
        return result

    def find_neighbors(self, current, obstacles):

        neighbors = []

        right_neighbor = current[:]
        right_neighbor[1] = current[1] + 1

        if 0 <= right_neighbor[1] < 25 and right_neighbor not in self.obstacles:
            neighbors.append(right_neighbor)

        left_neighbor = current[:]
        left_neighbor[1] = current[1] - 1

        if 0 <= left_neighbor[1] < 25 and left_neighbor not in self.obstacles:
            neighbors.append(left_neighbor)

        up_neighbor = current[:]
        up_neighbor[0] = current[0] + 1

        if 0 <= up_neighbor[0] < 25 and up_neighbor not in self.obstacles:

            neighbors.append(up_neighbor)

        down_neighbor = current[:]
        down_neighbor[0] = current[0] - 1

        if 0 <= down_neighbor[0] < 25 and down_neighbor not in self.obstacles:

            neighbors.append(down_neighbor)

        down_right_neighbor = current[:]
        down_right_neighbor[0] = current[0] + 1
        down_right_neighbor[1] = current[1] + 1

        if 0 <= down_right_neighbor[0] < 25 and 0 <= down_right_neighbor[1] < 25 and down_right_neighbor not in self.obstacles:
            neighbors.append(down_right_neighbor)

        up_right_neighbor = current[:]
        up_right_neighbor[0] = current[0] - 1
        up_right_neighbor[1] = current[1] + 1

        if 0 <= up_right_neighbor[0] < 25 and 0 <= up_right_neighbor[1] < 25 and up_right_neighbor not in self.obstacles:

            neighbors.append(up_right_neighbor)

        up_left_neighbor = current[:]
        up_left_neighbor[0] = current[0] - 1
        up_left_neighbor[1] = current[1] - 1

        if 0 <= up_left_neighbor[0] < 25 and 0 <= up_left_neighbor[1] < 25 and up_left_neighbor not in self.obstacles:

            neighbors.append(up_left_neighbor)

        down_left_neighbor = current[:]
        down_left_neighbor[0] = current[0] + 1
        down_left_neighbor[1] = current[1] - 1

        if 0 <= down_left_neighbor[0] < 25 and 0 <= down_left_neighbor[1] < 25 and down_left_neighbor not in self.obstacles:
            neighbors.append(down_left_neighbor)

        return neighbors

    def sort_open_set(self, open_set, f_score):
        index_to_fscore = []

        for node in open_set:
            f_score_of_node = f_score[node[0]][node[1]]
            index_to_fscore.append(f_score_of_node)

        sorted_copy = index_to_fscore.copy()
        sorted_copy.sort()

        sorted_open_set = []

        for value in sorted_copy:
            min = index_to_fscore.index(value)
            sorted_open_set.append(open_set[min])
            index_to_fscore[min] = float('inf') 
        return sorted_open_set

    def reconstruct_path(self, cameFrom, current):
        self.total_path = []

        while current != self.start:

            self.buttons[current[0]][current[1]].configure(bg='red')

            self.total_path.append(current[:])
            current = cameFrom[current[0]][current[1]]

    

    def a_star_algorithm(self, start, goal):
        self.start = start
        self.goal = goal
        execution.my_a_star_algorithm(self, self.start, self.goal)
   

    def find_path(self, event):

        if self.mode == 2:
            self.a_star_algorithm(self.start, self.goal)
            self.disable_buttons()

    def reset(self, event):

        if self.mode == 2:
            self.start = []
            self.goal = []
            self.obstacles = []
            self.mode = 0

            for i in range(25):
                for j in range(25):

                    self.buttons[i][j].configure(bg='SystemButtonFace')

            self.enable_buttons()
