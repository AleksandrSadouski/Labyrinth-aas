<?php
namespace App\Services;

class MazeService 
{
    private array $maze;
    private int $size;
    private array $entry = [];
    private array $exit = [];
    private float $branchWeight;   // Вес ветвления (0-1): выше = больше развилок
    private float $hallwayWeight;  // Вес длинных коридоров: выше = длиннее коридоры
    
    public function __construct(int $size, float $branchWeight, float $hallwayWeight) {
        if ($size % 2 == 0) $size++;
        $this->size = $size;
        $this->branchWeight = $branchWeight;
        $this->hallwayWeight = $hallwayWeight;
        $this->maze = [];
        
        $this->steny();
        $this->generateMazeGrowingTree();
        $this->createEntryAndExit();
    }
    
    private function steny(): void {
        for ($i = 0; $i < $this->size; $i++) {
            for ($j = 0; $j < $this->size; $j++) {
                $this->maze[$i][$j] = 1;
            }
        }
    }
    
    private function generateMazeGrowingTree(): void {
        $directions = [
            [0, -2], [0, 2], [-2, 0], [2, 0]
        ];
        
        $startX = 1 + 2 * rand(0, (int)(($this->size - 2) / 2));
        $startY = 1 + 2 * rand(0, (int)(($this->size - 2) / 2));
        
        $this->maze[$startX][$startY] = 0;
        
        $active = [[$startX, $startY]];
        
        while (!empty($active)) {
            $index = $this->selectActiveCell($active);
            $current = $active[$index];
            $x = $current[0];
            $y = $current[1];
            
            $unvisitedNeighbors = [];
            foreach ($directions as $dir) {
                $nx = $x + $dir[0];
                $ny = $y + $dir[1];
                
                if ($nx > 0 && $nx < $this->size-1 && $ny > 0 && $ny < $this->size-1 && $this->maze[$nx][$ny] == 1) {
                    $unvisitedNeighbors[] = [$nx, $ny, $dir[0]/2, $dir[1]/2];
                }
            }
            
            if (!empty($unvisitedNeighbors)) {
                $randomIndex = array_rand($unvisitedNeighbors);
                $neighbor = $unvisitedNeighbors[$randomIndex];
                $nx = $neighbor[0];
                $ny = $neighbor[1];
                $wallX = $x + $neighbor[2];
                $wallY = $y + $neighbor[3];
                
                $this->maze[$wallX][$wallY] = 0;
                $this->maze[$nx][$ny] = 0;
                
                $active[] = [$nx, $ny];
            } else {
                array_splice($active, $index, 1);
            }
        }
        
        $this->addDeadEnds();
    }
    
    private function selectActiveCell(array $active): int {
        $rand = mt_rand() / mt_getrandmax();
        
        if ($rand < $this->branchWeight) {
            return array_rand($active);
        } else {
            return count($active) - 1;
        }
    }
    
    private function addDeadEnds(): void {
        $deadEndProbability = 0.15;
        
        for ($i = 2; $i < $this->size - 2; $i++) {
            for ($j = 2; $j < $this->size - 2; $j++) {
                if ($this->maze[$i][$j] == 1) {
                    $passageCount = 0;
                    $directions = [[0, 1], [0, -1], [1, 0], [-1, 0]];
                    
                    foreach ($directions as $dir) {
                        $ni = $i + $dir[0];
                        $nj = $j + $dir[1];
                        if ($ni >= 0 && $ni < $this->size && $nj >= 0 && $nj < $this->size && $this->maze[$ni][$nj] == 0) {
                            $passageCount++;
                        }
                    }
                    
                    if ($passageCount == 3 && mt_rand(1, 100) <= $deadEndProbability * 100) {
                        $this->maze[$i][$j] = 0;
                    }
                }
            }
        }
    }
    
    private function createEntryAndExit(): void {
        $this->maze[0][1] = 0;
        $this->entry = [0, 1];
        $this->maze[$this->size - 1][$this->size - 2] = 0;
        $this->exit = [$this->size - 1, $this->size - 2];
    }
    
    public function getMaze(): array {
        return $this->maze;
    }
    
    public function getEntry(): array {
        return $this->entry;
    }
    
    public function getExit(): array {
        return $this->exit;
    }
}