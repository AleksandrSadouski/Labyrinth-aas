<?php
namespace App\Services\Shared;

class CodeGeneratorService
{
    private const BUKVAR = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private const CODE_LENGTH = 6;

    public function generate(): string
    {
        $roomCode = '';
        for ($i = 0; $i < self::CODE_LENGTH; $i++)
        {
        $randIndex = random_int(0, strlen(self::BUKVAR) - 1);
        $roomCode = $roomCode . self::BUKVAR[$randIndex];
        }
        return $roomCode;
    }
}