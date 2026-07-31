<?php
namespace App\Services;

class CodeGeneratorService
{
    private string $bukvar = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private string $roomCode = '';

    public function generate(): string
    {
        for ($i = 0; $i < 6; $i++)
        {
        $randIndex = random_int(0, strlen($bukvar) - 1);
        $this->roomCode = $this->roomCode . $this->bukvar[$randIndex];
        }
        return $this->roomCode;
    }
}