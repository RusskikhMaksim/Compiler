<?php
declare(strict_types=1);


class LexicalAnalyze
{
    public function letter(string $symbol): bool
    {

        if (ctype_alpha($symbol)) { //проверяем, является ли входящий символ буквой с помощью встроенной функции
            return true;
        } else {
            return false;
        }
    }

    public function digit(string $symbol): bool
    {
        if (ctype_digit($symbol)) {
            return true;
        } else {
            return false;
        }
    }
}