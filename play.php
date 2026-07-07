<?php

define('GRID_WIDTH', 8);
define('GRID_HEIGHT', 6);

$grid = [
    ['#', '#', '#', '#', '#', '#', '#', '#'],
    ['#', '.', '.', '.', '.', '.', '.', '#'],
    ['#', '.', '#', '#', '#', '.', '.', '#'],
    ['#', '.', '.', '.', '#', '.', '#', '#'],
    ['#', 'X', '#', '.', '.', '.', '.', '#'],
    ['#', '#', '#', '#', '#', '#', '#', '#'],
];

$startX = 4;
$startY = 1;

function printGrid(array $grid, array $overlay = []) {
    echo "\n  +-----------------+\n";
    for ($r = 0; $r < GRID_HEIGHT; $r++) {
        echo "  | ";
        for ($c = 0; $c < GRID_WIDTH; $c++) {
            $char = $grid[$r][$c];
            if (isset($overlay[$r][$c])) {
                $char = $overlay[$r][$c];
            }
            if ($char === 'X') {
                echo "\033[1;33mX\033[0m ";
            } elseif ($char === '$') {
                echo "\033[1;32m$\033[0m ";
            } elseif ($char === '#') {
                echo "\033[0;90m#\033[0m ";
            } elseif ($char === '*') {
                echo "\033[1;31m*\033[0m ";
            } else {
                echo ". ";
            }
        }
        echo "|\n";
    }
    echo "  +-----------------+\n\n";
}

function toCartesian(int $row, int $col): string {
    $x = $col;
    $y = (GRID_HEIGHT - 1) - $row;
    return "({$x}, {$y})";
}

function findProbableLocations(array $grid, int $startRow, int $startCol): array {
    $locations = [];
    
    for ($a = 1; $startRow - $a >= 0; $a++) {
        $r1 = $startRow - $a;
        $c1 = $startCol;
        
        if ($grid[$r1][$c1] === '#') {
            break;
        }
        
        for ($b = 1; $c1 + $b < GRID_WIDTH; $b++) {
            $r2 = $r1;
            $c2 = $c1 + $b;
            
            if ($grid[$r2][$c2] === '#') {
                break;
            }
            
            for ($c = 1; $r2 + $c < GRID_HEIGHT; $c++) {
                $r3 = $r2 + $c;
                $c3 = $c2;
                
                if ($grid[$r3][$c3] === '#') {
                    break;
                }
                
                if ($grid[$r3][$c3] === '.') {
                    $locations[] = [
                        'row' => $r3,
                        'col' => $c3,
                        'a' => $a,
                        'b' => $b,
                        'c' => $c
                    ];
                }
            }
        }
    }
    
    return $locations;
}

function tracePath(array $grid, int $startRow, int $startCol, int $a, int $b, int $c): array {
    $path = [];
    $currRow = $startRow;
    $currCol = $startCol;
    
    for ($i = 1; $i <= $a; $i++) {
        $currRow--;
        if ($currRow < 0 || $grid[$currRow][$currCol] === '#') {
            return [
                'success' => false,
                'error' => "Hit obstacle '#' or boundary at grid Row {$currRow}, Col {$currCol} moving UP.",
                'path' => $path
            ];
        }
        $path[$currRow][$currCol] = '*';
    }
    
    for ($i = 1; $i <= $b; $i++) {
        $currCol++;
        if ($currCol >= GRID_WIDTH || $grid[$currRow][$currCol] === '#') {
            return [
                'success' => false,
                'error' => "Hit obstacle '#' or boundary at grid Row {$currRow}, Col {$currCol} moving RIGHT.",
                'path' => $path
            ];
        }
        $path[$currRow][$currCol] = '*';
    }
    
    for ($i = 1; $i <= $c; $i++) {
        $currRow++;
        if ($currRow >= GRID_HEIGHT || $grid[$currRow][$currCol] === '#') {
            return [
                'success' => false,
                'error' => "Hit obstacle '#' or boundary at grid Row {$currRow}, Col {$currCol} moving DOWN.",
                'path' => $path
            ];
        }
        $path[$currRow][$currCol] = '*';
    }
    
    return [
        'success' => true,
        'row' => $currRow,
        'col' => $currCol,
        'path' => $path
    ];
}

echo "\033[1;36m====================================================\033[0m\n";
echo "\033[1;36m           HIDDEN ITEM GRID SOLVER & GAME           \033[0m\n";
echo "\033[1;36m====================================================\033[0m\n";

echo "Initial Grid Layout (X = starting position):\n";
printGrid($grid);

while (true) {
    echo "Select an option:\n";
    echo "1. List probable item locations (Solve Puzzle)\n";
    echo "2. Input custom A, B, C steps (Interactive Navigation)\n";
    echo "3. Exit\n";
    echo "Enter choice (1-3): ";
    
    $choice = trim(fgets(STDIN));
    
    if ($choice === '1') {
        $solutions = findProbableLocations($grid, $startX, $startY);
        
        echo "\n\033[1;32m--- Found " . count($solutions) . " Probable Coordinates ---\033[0m\n";
        
        $overlay = [];
        $uniqueCoords = [];
        
        foreach ($solutions as $sol) {
            $key = "{$sol['row']},{$sol['col']}";
            $uniqueCoords[$key] = [
                'row' => $sol['row'],
                'col' => $sol['col']
            ];
            $overlay[$sol['row']][$sol['col']] = '$';
            
            echo " - Path: Up {$sol['a']} -> Right {$sol['b']} -> Down {$sol['c']} ";
            echo "=> Grid: [{$sol['row']}, {$sol['col']}], Cartesian: " . toCartesian($sol['row'], $sol['col']) . "\n";
        }
        
        echo "\n\033[1;32mSummary of Coordinates:\033[0m\n";
        foreach ($uniqueCoords as $coord) {
            echo " * Grid: \033[1m[{$coord['row']}, {$coord['col']}]\033[0m | Cartesian: \033[1m" . toCartesian($coord['row'], $coord['col']) . "\033[0m\n";
        }
        
        echo "\nGrid with probable item locations marked with \033[1;32m$\033[0m:\n";
        printGrid($grid, $overlay);
        echo "--------------------------------------------------\n\n";
        
    } elseif ($choice === '2') {
        echo "\n\033[1;33m--- Interactive Path Trace ---\033[0m\n";
        echo "Enter steps to navigate:\n";
        
        echo "1. Up/North steps (A): ";
        $a = (int)trim(fgets(STDIN));
        
        echo "2. Right/East steps (B): ";
        $b = (int)trim(fgets(STDIN));
        
        echo "3. Down/South steps (C): ";
        $c = (int)trim(fgets(STDIN));
        
        if ($a <= 0 || $b <= 0 || $c <= 0) {
            echo "\033[1;31mError: All steps (A, B, C) must be positive integers >= 1.\033[0m\n\n";
            continue;
        }
        
        echo "\nTracing path: Up {$a} -> Right {$b} -> Down {$c}...\n";
        $trace = tracePath($grid, $startX, $startY, $a, $b, $c);
        
        if ($trace['success']) {
            echo "\033[1;32mSUCCESS!\033[0m You landed on clear path at:\n";
            echo " - Grid: [{$trace['row']}, {$trace['col']}]\n";
            echo " - Cartesian: " . toCartesian($trace['row'], $trace['col']) . "\n";
            
            $overlay = $trace['path'];
            $overlay[$trace['row']][$trace['col']] = '$';
            printGrid($grid, $overlay);
        } else {
            echo "\033[1;31mFAILED!\033[0m {$trace['error']}\n";
            printGrid($grid, $trace['path']);
        }
        echo "--------------------------------------------------\n\n";
        
    } elseif ($choice === '3') {
        echo "Thank you for playing!\n";
        break;
    } else {
        echo "\033[1;31mInvalid option. Please choose 1, 2, or 3.\033[0m\n\n";
    }
}
