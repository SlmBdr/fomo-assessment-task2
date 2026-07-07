# Task 2: Hidden Item Game

A command-line interface (CLI) program written in PHP to solve a grid navigation puzzle.

## Puzzle Details
The game grid consists of:
- `#` representing an obstacle.
- `.` representing a clear path.
- `X` representing the player's starting position (located at Row 4, Col 1).

### Navigation Rules
The player must navigate in a specific order:
1. **Up/North** $A$ step(s) ($A \ge 1$), then
2. **Right/East** $B$ step(s) ($B \ge 1$), then
3. **Down/South** $C$ step(s) ($C \ge 1$).

All steps of the path must be traversed through clear path cells (`.`) without hitting any obstacles (`#`) or boundaries.

---

## How to Run

Run the game directly from your terminal using:
```bash
php play.php
```

---

## Program Features

### 1. Solve Puzzle (Option 1)
Finds and outputs all probable coordinates where the hidden item could be located. It outputs coordinates in two systems:
1. **Grid Coordinates**: 0-indexed, where `[0,0]` is the top-left corner.
2. **Cartesian Coordinates**: Where `(0,0)` is the bottom-left corner.

It also displays the grid layout with all probable item locations marked with a green `$` symbol.

**Discovered Coordinates:**
* Path: Up 1 -> Right 2 -> Down 1 => Grid: `[4, 3]`, Cartesian: `(3, 1)`
* Path: Up 3 -> Right 4 -> Down 1 => Grid: `[2, 5]`, Cartesian: `(5, 3)`
* Path: Up 3 -> Right 4 -> Down 2 => Grid: `[3, 5]`, Cartesian: `(5, 2)`
* Path: Up 3 -> Right 4 -> Down 3 => Grid: `[4, 5]`, Cartesian: `(5, 1)`
* Path: Up 3 -> Right 5 -> Down 1 => Grid: `[2, 6]`, Cartesian: `(6, 3)`

### 2. Interactive Navigation (Option 2)
Allows you to enter your own steps $A$, $B$, $C$. The program traces the path and prints:
- Whether the movement succeeded.
- The path taken (marked with red `*` symbols on the grid).
- Where you landed (grid index and Cartesian).
- If it failed, which obstacle it hit.
