from ast import If
from distutils.dep_util import newer_group
from tkinter import *
from webbrowser import open_new
from Astar import Astar
from functools import partial
from time import sleep

def my_a_star_algorithm(self, start, goal):
    
    open_set = [start]

    g_score = []
    f_score = []
    came_from = []

    # g_score, came_from을 infinity로 초기화
    for i in range(25):
        f_score.append([])
        g_score.append([])
        came_from.append([])
        for j in range(25):
            temp = float('inf')
            came_from[i].append([])
            g_score[i].append(temp) 
            f_score[i].append(temp)

    # 첫 시작점에 대한 g_score, f_score값 정의
    g_score[start[0]][start[1]] = 0
    f_score[start[0]][start[1]] = self.heuristic(start, goal)
    
    while len(open_set) > 0:
        self.master.update_idletasks()
        sleep(0.02)

        open_set = self.sort_open_set(open_set, f_score)
        current = open_set[0]
        current_row = current[0]
        current_column = current[1]

        if current == goal:
            # current 노드가 goal일 때, reconstruct_path() 호출
            # reconstruct_path(): goal부터 start까지 경로를 역추적
            # red로 표시, total_path에 해당 노드 추가
            self.reconstruct_path(came_from, current)
            # total_path 노드 리스트를 prompt에 출력
            print("total_path =", self.total_path) 
            return

        open_set.remove(current)
        # current에서 find_neighbors() 함수 호출
        # current에 대해 유효한 이웃 노드 리스트 return
        neighbors = self.find_neighbors(current, self.obstacles)

        # neighbors의 노드들 중 open_set에 넣을 노드 select
        for neigh_node in neighbors:
            bt = self.buttons[neigh_node[0]][neigh_node[1]]
            # blue가 아닌 버튼들에 대해 해당 노드 open_set에 추가
            if bt['bg'] != 'blue':
                open_set.append(neigh_node)
                bt.configure(bg='blue') # 버튼을 blue로 설정
                
                # open_set에 추가된 노드에 대해 came_from 값 업데이트
                came_from[neigh_node[0]][neigh_node[1]] = current
                
                # g_score, f_score 값 업데이트
                g_score[neigh_node[0]][neigh_node[1]] = g_score[current[0]][current[1]] + 1
                h_score = self.heuristic(neigh_node, goal)
                f_score[neigh_node[0]][neigh_node[1]] = g_score[neigh_node[0]][neigh_node[1]] + h_score

    print("실패!")

if __name__ == '__main__':
    root = Tk()
    app = Astar.App(root)

    # Starts the algorithm when we press enter
    root.bind('<Return>', app.find_path)
    # Resets when we press 'R'
    root.bind('r', app.reset)

    root.mainloop()
