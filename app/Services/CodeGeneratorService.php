<?php
namespace App\Services;

class CodeGeneratorService
{
    private string $bukvar = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    public function generate(): string
    {
        $roomCode = '';
        for ($i = 0; $i < 6; $i++)
        {
        $randIndex = random_int(0, strlen($this->bukvar) - 1);
        $roomCode = $roomCode . $this->bukvar[$randIndex];
        }
        return $roomCode;
    }
}