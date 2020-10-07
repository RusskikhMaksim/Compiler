<?php
declare(strict_types=1);


class LexicalAnalyze
{
    public function letter(string $symbol): bool
    {

        if (ctype_alpha($symbol)) { //проверяем, является ли входящий символ буквой с помощью встроенной функции
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function digit(string $symbol): bool
    {
        if (ctype_digit($symbol)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function nextLine(string $symbol): bool
    {
        if ($symbol === "\n"){
            return TRUE;
        } else {
            return FALSE;
        }

    }
}