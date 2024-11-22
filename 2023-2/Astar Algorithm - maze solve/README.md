# A* Algorithm Visualization

This project is a Python program that visualizes the A* algorithm. Users can set the start point, end point, and obstacles, and visually observe the process of the A* algorithm finding the shortest path.

## Installation

1. Clone this repository:
    ```sh
    git clone https://github.com/yourusername/astar-visualization.git
    cd astar-visualization/Astar
    ```

2. Install the required packages:
    ```sh
    pip install -r requirements.txt
    ```

## Usage

1. Run the program:
    ```sh
    python execution.py
    ```

2. When the program runs, a popup window will appear. Follow the instructions in the popup window to set the start point, end point, and obstacles.

3. Once the setup is complete, press the Enter key to start the A* algorithm.

4. When the algorithm is complete, the shortest path will be highlighted in red. Press the R key to restart.

## File Description

- `Astar/Astar.py`: Implements the GUI and logic for the A* algorithm.
- `execution.py`: Entry point of the program, initializes the GUI and binds events.

## Main Classes and Functions

### `Astar/Astar.py`

- `App` class: Contains the GUI and A* algorithm logic.
  - `__init__(self, master)`: Initializes the GUI and sets up buttons.
  - `enable_buttons(self)`: Enables the buttons.
  - `disable_buttons(self)`: Disables the buttons.
  - `button_operation(self, row, column)`: Sets the start point, end point, and obstacles.
  - `add_obstacle(self, row, column, event)`: Adds obstacles.
  - `heuristic(self, node1, node2)`: Heuristic function.
  - `find_neighbors(self, current, obstacles)`: Finds neighboring nodes.
  - `sort_open_set(self, open_set, f_score)`: Sorts the open set.
  - `reconstruct_path(self, cameFrom, current)`: Reconstructs the path.
  - `a_star_algorithm(self, start, goal)`: Executes the A* algorithm.
  - `find_path(self, event)`: Starts finding the path.
  - `reset(self, event)`: Resets the setup.

- `center_gui(root)`: Centers the GUI window.
- `pop_up_window(app)`: Displays the startup popup window.

### `execution.py`

- `my_a_star_algorithm(self, start, goal)`: Executes the A* algorithm logic.
