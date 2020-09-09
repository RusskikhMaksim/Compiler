<?php
declare(strict_types=1);
require_once 'LexicalAnalyze.php';
require_once 'CompleteToken.php';


class TokenFormation
{
    public string $bodyOfToken = "";
    public string $nextSymbol = "";
    private object $checkingNextChar;
    public object $token;
    public $keyWordsArray = array(
        "#include", "if", "else if", "else", "while", "for", "return", "stdio", "stdlib", "string", "switch", "case"
    );
    public $dataTypesArray = array(
        "int", "char", "float", "double", "void", "short"
    );

    public function __construct()
    {
        $this->checkingNextChar = new LexicalAnalyze();
        $this->token = new CompleteToken();
    }

    public function letterToken(string $symbol, int $arrayIndexOfCurrentElement, array $programCopy, int $stringCounter, int $positionInStrIndex): object
    {

        $this->token->startPositionInString = $positionInStrIndex;

        $this->bodyOfToken = $symbol;

        while (true) {
            if ($this->checkingNextChar->letter($programCopy[$arrayIndexOfCurrentElement + 1])) {
                $this->nextSymbol = $programCopy[$arrayIndexOfCurrentElement + 1];
                $arrayIndexOfCurrentElement++;
                $positionInStrIndex++;
                $this->bodyOfToken .= $this->nextSymbol;
            } elseif ($this->checkingNextChar->digit($programCopy[$arrayIndexOfCurrentElement + 1])) {
                $this->nextSymbol = $programCopy[$arrayIndexOfCurrentElement + 1];
                $arrayIndexOfCurrentElement++;
                $positionInStrIndex++;
                $this->bodyOfToken .= $this->nextSymbol;
            } else {
                $this->token->bodyOfToken = $this->bodyOfToken;
                $this->token->tokenClass = "id";
                $this->token->endPositionInString = $positionInStrIndex;
                $this->token->NumOfStringInProgram = $stringCounter;

                foreach ($this->dataTypesArray as $dataType) {
                    if (strcmp($dataType, $this->token->bodyOfToken) === 0) {
                        $this->token->tokenClass = "datatype";
                        break;
                    }
                }

                foreach ($this->keyWordsArray as $keyWord) {
                    if (strcmp($keyWord, $this->token->bodyOfToken) === 0) {
                        $this->token->tokenClass = "KeyWord " . $keyWord;
                        break;
                    }
                }

                if ($this->token->tokenClass === "else" && $programCopy[$this->token->arrayIndexOfCurrentElement + 2] === "i" && $programCopy[$this->token->arrayIndexOfCurrentElement + 3] === "f") {
                    $this->token->tokenClass = "KeyWord " . $this->keyWordsArray[2]; //else if
                    $this->token->bodyOfToken .= $programCopy[$this->token->arrayIndexOfCurrentElement + 1] . $programCopy[$this->token->arrayIndexOfCurrentElement + 2] . $programCopy[$this->token->arrayIndexOfCurrentElement + 3];
                    $this->token->endPositionInString = $this->token->endPositionInString + 3;

                }

                return $this->token;
            }
        }

    }


    public function digitToken(string $symbol, int $arrayIndexOfCurrentElement, array $programCopy, int $stringCounter, int $positionInStrIndex): object
    {

        $this->token->startPositionInString = $positionInStrIndex;

        $this->bodyOfToken = $symbol;

        while (true) {
            if ($this->checkingNextChar->digit($programCopy[$arrayIndexOfCurrentElement + 1])) {
                $this->nextSymbol = $programCopy[$arrayIndexOfCurrentElement + 1];
                $arrayIndexOfCurrentElement++;
                $positionInStrIndex++;
                $this->bodyOfToken .= $this->nextSymbol;
            } else {
                $this->token->bodyOfToken = $this->bodyOfToken;
                $this->token->tokenClass = "numeric_constant";
                $this->token->endPositionInString = $positionInStrIndex;
                $this->token->NumOfStringInProgram = $stringCounter;

                return $this->token;
            }
        }

    }

    public function allOtherTokens(string $symbol, int $arrayIndexOfCurrentElement, array $programCopy, int $stringCounter, int $positionInStrIndex): object
    {

        switch ($symbol) {
            case '(':
                $this->token->setParameters($symbol, "LParen", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case ')':
                $this->token->setParameters($symbol, "RParen", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case '{':
                $this->token->setParameters($symbol, "l_brace", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case '}':
                $this->token->setParameters($symbol, "r_brace", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case '[':
                $this->token->setParameters($symbol, "l_sqparen", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case ']':
                $this->token->setParameters($symbol, "r_sqparen", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case ';':
                $this->token->setParameters($symbol, "semi", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case ':':
                $this->token->setParameters($symbol, "colon", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case '#':
                $this->token = $this->letterToken($symbol, $arrayIndexOfCurrentElement, $programCopy, $stringCounter, $positionInStrIndex);
                break;
            case '.':
                $this->token->setParameters($symbol, "point", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case ',':
                $this->token->setParameters($symbol, "comma", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case '<':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === "<") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "bit_l_shift", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);

                } elseif ($programCopy[$arrayIndexOfCurrentElement + 1] === "=") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "less_t_or_eq", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);

                } else {
                    $this->token->setParameters($symbol, "less than", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            case '>':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === ">") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "bit_r_shift", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);

                } elseif ($programCopy[$arrayIndexOfCurrentElement + 1] === "=") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "more_t_or_eq", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);

                } else {
                    $this->token->setParameters($symbol, "more than", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            case '"':
                $lexeme = $symbol;
                $i = $arrayIndexOfCurrentElement + 1;
                $positionInStrIndexCopy = $positionInStrIndex + 1;
                while ($programCopy[$i + 1] !== "\n") {
                    $lexeme .= $programCopy[$i];

                    if ($programCopy[$i] === '"') {
                        $this->token->setParameters($lexeme, "string litheral", $positionInStrIndex, $positionInStrIndexCopy, $stringCounter);
                        break;
                    } elseif ($programCopy[$i] !== '"' && $programCopy[$i + 1] === "\n") {
                        $this->token->setParameters($lexeme, "unknown", $positionInStrIndex, $positionInStrIndexCopy, $stringCounter);
                        break;
                    }
                    $i++;
                    $positionInStrIndexCopy++;
                }
                break;

            case '!':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === "=") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "not equal", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);

                } elseif (LexicalAnalyze::letter($programCopy[$arrayIndexOfCurrentElement + 1]) || LexicalAnalyze::digit($programCopy[$arrayIndexOfCurrentElement + 1])) {
                    $this->token->setParameters($symbol, "logical negation", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            case '+':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === "+") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "increment", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);
                } else {
                    $this->token->setParameters($symbol, "plus", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            case '-':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === "-") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "decrement", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);
                } else {
                    $this->token->setParameters($symbol, "minus", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            case '*':
                $this->token->setParameters($symbol, "multiply", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case '/':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === "/") {
                    //skip one-line comments
                    $i = $arrayIndexOfCurrentElement + 1;
                    $positionInStrIndexCopy = $positionInStrIndex + 1;
                    while ($programCopy[$i + 1] !== "\n") {
                        $i++;
                        $positionInStrIndexCopy++;
                    }
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "one-line comments", $positionInStrIndex, $positionInStrIndexCopy, $stringCounter);
                } elseif ($programCopy[$arrayIndexOfCurrentElement + 1] === "*") {
                    $commentsAreClosed = false;
                    $commentsAreClosedIndex = 0;
                    $lastIndex = count($programCopy) - 1;
                    $checkForACloseIndex = $arrayIndexOfCurrentElement + 2;
                    for ($checkForACloseIndex; $checkForACloseIndex <= $lastIndex; $checkForACloseIndex++) {
                        if ($programCopy[$checkForACloseIndex] === "*" && $programCopy[$checkForACloseIndex + 1] === "/") {
                            $commentsAreClosed = true;
                            $checkForACloseIndex = $checkForACloseIndex + 1;
                            $commentsAreClosedIndex = $checkForACloseIndex;
                            break;
                        }
                    }
                    if ($commentsAreClosed === true) {
                        $lexeme = $symbol;
                        $i = $arrayIndexOfCurrentElement + 1;
                        $positionInStrIndexCopy = $positionInStrIndex + 1;
                        for ($i; $i <= $commentsAreClosedIndex; $i++) {
                            $lexeme .= $programCopy[$i];
                            $positionInStrIndexCopy++;
                        }
                        $this->token->setParameters($lexeme, "multiline comments", $positionInStrIndex, $positionInStrIndexCopy, $stringCounter);
                    } elseif ($commentsAreClosed === false) {
                        $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                        $this->token->setParameters($lexeme, "unknown", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);
                    }

                } else {
                    $this->token->setParameters($symbol, "divide", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            case '%':
                $this->token->setParameters($symbol, "modulo", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;
            case '|':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === "|") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "logical addition", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);
                } else {
                    $this->token->setParameters($symbol, "bitwise_or", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            case '&':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === "&") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "logical multiplication", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);
                } else {
                    $this->token->setParameters($symbol, "bitwise_and", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            case '=':
                if ($programCopy[$arrayIndexOfCurrentElement + 1] === "=") {
                    $lexeme = $symbol . $programCopy[$arrayIndexOfCurrentElement + 1];
                    $this->token->setParameters($lexeme, "comparison", $positionInStrIndex, $positionInStrIndex + 1, $stringCounter);
                } else {
                    $this->token->setParameters($symbol, "assignment", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                }
                break;
            default:
                $this->token->setParameters($symbol, "unknown", $positionInStrIndex, $positionInStrIndex, $stringCounter);
                break;


        }
        return $this->token;
    }
}