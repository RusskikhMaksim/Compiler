<?php
/*
 *
 *
 */

class SyntaxErrorHandler
{
    public array $Tokens;
    public int $tokenArrayIndex;
    public int $tokenArrayIndexCopy;
    public int $nestingLevelCounter;

    public function __construct(array $Tokens)
    {
        $this->Tokens = $Tokens;
    }

    public function checkBracketsValidation(string $typeOfNode){
        $arrayOfBrackets = array($this->Tokens[$this->tokenArrayIndex]);
        $this->tokenArrayIndexCopy = $this->tokenArrayIndex + 1;
        $brackets = "";
        if($typeOfNode === "init braces check"){
            $bracketsCounter = 0;
            $sqBracketsCounter = 0;
        } else {
        $bracketsCounter = 1;
        $sqBracketsCounter = 1;
        }
        $currentToken = $this->getNextToken();

        while ($currentToken->bodyOfToken !== "\\n"){
            if($currentToken->bodyOfToken === "("){
                $arrayOfBrackets[] = $currentToken;
                $bracketsCounter++;
            } elseif ($currentToken->bodyOfToken === "["){
                $arrayOfBrackets[] = $currentToken;
                $sqBracketsCounter++;
            } elseif ($currentToken->bodyOfToken === ")"){
                $arrayOfBrackets[] = $currentToken;
                $bracketsCounter--;
            } elseif ( $currentToken->bodyOfToken === "]"){
                $arrayOfBrackets[] = $currentToken;
                $sqBracketsCounter--;
            }

            $currentToken = $this->getNextToken();

        }

        for ($i = 0; $i <= count($arrayOfBrackets) - 1; $i++){
            if($arrayOfBrackets[$i]->bodyOfToken === "["){
                if($arrayOfBrackets[$i + 1]->bodyOfToken !== "]" && $arrayOfBrackets[$i + 2]->bodyOfToken !== "]"){
                    throw new MissedSqRParenException("Expected \"]\" to match this \"[\"\n");
                }
            } elseif ($arrayOfBrackets[$i]->bodyOfToken === "]"){
                if($arrayOfBrackets[$i - 1]->bodyOfToken !== "[" && $arrayOfBrackets[$i - 2]->bodyOfToken !== "["){
                    throw new MissedSqLParenException("Expected \"[\" to match this \"]\"\n");
                }
            }
        }

        if($typeOfNode === "inOrOut") {
            $lexeme = $this->Tokens[($this->tokenArrayIndexCopy) - 3];
            $lexemeIfSemicolonMissed = $this->Tokens[($this->tokenArrayIndexCopy) - 2];
        }else{
            $lexeme = $this->Tokens[($this->tokenArrayIndexCopy) - 3];
        }
        //$this->Tokens[($this->tokenArrayIndexCopy) - 2]->bodyOfNode
        if($typeOfNode === "inOrOut") {
            if ($lexeme->bodyOfToken !== ")" && $lexemeIfSemicolonMissed->bodyOfToken !== ")") {
                throw new MissedRParenException("Expected \")\" to match this \"(\"\n");
            }
        }else{
            if($typeOfNode !== "init braces check") {
                if ($lexeme->bodyOfToken !== ")") {
                    throw new MissedRParenException("Expected \")\" to match this \"(\"\n");
                }
            }
        }
        if($bracketsCounter > 0){
            throw new UnexpectedLParenException("Unexpected \"(\"");
        } elseif ($bracketsCounter < 0){
            throw new UnexpectedRParenException("Unexpected \")\"");
        }

    }

    public function checkDeclareSyntax(){
        $expectedTokens = array(",", ";", "[", "=", "(");
        $actualToken = $this->viewNextToken();
        $actualLexeme = $actualToken->bodyOfToken;
        $syntaxError = TRUE;
        foreach ($expectedTokens as $token){
            if($token === $actualLexeme){
                $syntaxError = FALSE;
                break;
            }
        }
        if($syntaxError === TRUE) {
            $this->tokenArrayIndex += 1;
            $actualToken = $this->viewNextToken();
            $actualLexeme = $actualToken->bodyOfToken;
            throw new MissedLexemeException("Unexpected \"$actualLexeme\"");
        }
    }

    public function checkReturnSyntax($previousToken, $expectedTokenClasses, $actualTokenClass){
        $syntaxError = TRUE;
        foreach ($expectedTokenClasses as $token){
            if($token === $actualTokenClass){
                $syntaxError = FALSE;
                break;
            }
        }
        if($syntaxError === TRUE) {
            $actualToken = $this->viewNextToken();
            $actualLexeme = $actualToken->bodyOfToken;
            throw new UnexpectedReturnValueException("Unexpected return value: \"$actualLexeme\"");
        }
    }

    public function checkAssigmentSyntax(){
        $expectedTokens = array("[", "=");
        $actualToken = $this->viewNextToken();
        $actualLexeme = $actualToken->bodyOfToken;
        $syntaxError = TRUE;
        foreach ($expectedTokens as $token){
            if($token === $actualLexeme){
                $syntaxError = FALSE;
                break;
            }
        }
        if($syntaxError === TRUE) {
            $this->tokenArrayIndex += 1;
            $actualToken = $this->viewNextToken();
            $actualLexeme = $actualToken->bodyOfToken;
            throw new MissedLexemeException("Unexpected \"$actualLexeme\"");
        }

        $currentToken = $this->getNextToken();
        $flag = 0;
        while($currentToken->bodyOfToken !== "\\n"){
            if($currentToken->bodyOfToken === ";"){
                $flag = 1;
            }
            $currentToken = $this->getNextToken();
        }
        if($flag == 0) {
            $actualToken = $this->getNextToken();
            $actualLexeme = $actualToken->bodyOfToken;
            throw new MissedLexemeException("Unexpected \"$actualLexeme\"");
        }
    }

    public function validateIdName($nameOfId){
        $firstSymbol = substr($nameOfId, 0, 1);
        if (ctype_alpha($firstSymbol)){
            return TRUE;
        } else {
            throw new Exception("uncorrect name of the variable '$nameOfId' ");
        }
    }


    public function setParametrs(int $tokenArrayIndex, int $nestingLevelCounter){
        $this->tokenArrayIndex = $tokenArrayIndex;
        $this->tokenArrayIndexCopy = $tokenArrayIndex;
        $this->nestingLevelCounter = $nestingLevelCounter;

    }

    public function checkForSyntaxErrors(string $typeOfNode, object $previousToken){
        //принимаем тип узла и запускаем соответствующую проверку
        //если ошибка, бросам исключение, ловим его, выключаем программу.
        if( $typeOfNode === "while" || $typeOfNode === "if" || $typeOfNode === "inOrOut") {
            $expectedToken = "(";
            $actualToken = $this->viewNextToken();
            $actualLexeme = $actualToken->bodyOfToken;

            if(isset($actualLexeme)) {
                try {

                    $this->checkLexeme($previousToken, $expectedToken, $actualLexeme);
                    $this->checkBracketsValidation($typeOfNode);

                    if($typeOfNode === "inOrOut"){
                        $actualToken = $this->Tokens[$this->tokenArrayIndexCopy - 2];
                        $actualLexeme = $actualToken->bodyOfToken;
                        $this->checkIfSemicolonMissed($actualLexeme);
                    }
                } catch (MissedLexemeException | MissedSqLParenException | UnexpectedLParenException $e) {
                    $errorMessage ="\033[31m" . $e->getMessage() . "on position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                } catch (MissedRParenException | UnexpectedRParenException | MissedSqRParenException $e){
                    $errorMessage = "\033[31m" . $e->getMessage() . " on position " . $this->Tokens[$this->tokenArrayIndexCopy - 1]->startPositionInString . " in line " . $this->Tokens[$this->tokenArrayIndexCopy - 1]->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }//добавить ошибку невозможности получения токена

        } elseif ($typeOfNode === "declaration"){
            try {
                $this->checkDeclareSyntax();
            } catch (MissedLexemeException $e){
                $actualToken = $this->viewNextToken();
                $errorMessage ="\033[31m" . $e->getMessage() . "on position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }
        } elseif ($typeOfNode === "variable declaration"){ //не нужно
            $expectedToken = ";";
            $actualToken = $this->viewNextToken();
            $actualLexeme = $actualToken->bodyOfToken;
            try {
                $this->checkLexeme($previousToken, $expectedToken, $actualLexeme);
            } catch (MissedLexemeException $e){
                $errorMessage ="\033[31m" . $e->getMessage() . "on position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }
        } elseif ($typeOfNode === "function declaration"){
            $this->tokenArrayIndexCopy = $this->tokenArrayIndex;
            $expectedToken = "{";
            $actualToken = $this->viewNextToken();
            $actualLexeme = $actualToken->bodyOfToken;

            if(isset($actualLexeme)) {
                try {
                    $this->checkLexeme($previousToken, $expectedToken, $actualLexeme);
                } catch (MissedLexemeException $e) {
                    $this->tokenArrayIndexCopy++;
                    $actualToken = $this->getNextToken();
                    $errorMessage ="\033[31m" . "Expected \"$expectedToken\" after \"$previousToken->bodyOfToken\", got \"$actualToken->bodyOfToken\" instead\n" . "on position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
                    print ($errorMessage);
                    exit();
                }
            }
        } elseif ($typeOfNode === "assigment"){

            try {

                $this->checkAssigmentSyntax();
            } catch (MissedLexemeException $e){

                $actualToken = $this->viewNextToken();
                $errorMessage ="\033[31m" . $e->getMessage() . "on position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }
        } elseif ($typeOfNode === "array initialization"){
            $expectedToken = "{";
            $actualToken = $this->viewNextToken();
            $actualLexeme = $actualToken->bodyOfToken;
            try{
                $this->checkLexeme($previousToken, $expectedToken, $actualLexeme);
                $semicolonMissed = true;
                //while($actualLexeme !== "\\n"){

                //}
            } catch (MissedLexemeException $e){
                $errorMessage ="\033[31m" . "Expected \"$expectedToken\" after \"$previousToken->bodyOfToken\", got \"$actualToken->bodyOfToken\" instead\n" . "on position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }

        } elseif ($typeOfNode === "init braces check"){
            //$expectedToken = "(";
            $actualToken = $this->viewNextToken();
            $actualLexeme = $actualToken->bodyOfToken;
            try {
                $this->checkBracketsValidation($typeOfNode);
            } catch (MissedLexemeException | MissedSqLParenException | UnexpectedLParenException $e) {
                $errorMessage ="\033[31m" . $e->getMessage() . "on position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            } catch (MissedRParenException | UnexpectedRParenException | MissedSqRParenException $e){
                $errorMessage = "\033[31m" . $e->getMessage() . " on position " . $this->Tokens[$this->tokenArrayIndexCopy - 1]->startPositionInString . " in line " . $this->Tokens[$this->tokenArrayIndexCopy - 1]->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }
        }elseif ($typeOfNode === "return"){
            $expectedTokenClasses = array("numeric_constant", "id");
            $actualToken = $this->viewNextToken();
            $actualTokenClass = $actualToken->tokenClass;
            try{
              $this->checkReturnSyntax($previousToken, $expectedTokenClasses, $actualTokenClass);
              $expectedToken = ";";
              $previousToken = $this->getNextToken();
              $actualToken = $this->getNextToken();
              $actualLexeme = $actualToken->bodyOfToken;
              $this->checkLexeme($previousToken, $expectedToken, $actualLexeme);
            } catch (MissedLexemeException | UnexpectedReturnValueException $e){
                $errorMessage ="\033[31m" . $e->getMessage() . " position " . $actualToken->startPositionInString . " in line " . $actualToken->NumOfStringInProgram;
                print ($errorMessage);
                exit();
            }
        }

    }

    public function viewNextToken(){
        return $this->Tokens[$this->tokenArrayIndex];
    }

    public function getNextToken(){
        return $this->Tokens[$this->tokenArrayIndexCopy++];
    }

    public function checkIfSemicolonMissed($actualToken){
        if(";" !== $actualToken){
            $actualToken = $this->getNextToken();
            $actualLexeme = $actualToken->bodyOfToken;
            throw new MissedLexemeException("Unexpected $actualLexeme\n");
        }
    }

    public function checkLexeme($previousToken ,$expectedToken, $actualToken){
        if($expectedToken !== $actualToken){
            throw new MissedLexemeException("Expected \"$expectedToken\" after \"$previousToken->bodyOfToken\", got \"$actualToken\" instead\n");
        }
    }

    public function checkTokenClass($previousToken, $expectedTokenClass, $actualTokenClass){
        if($expectedTokenClass !== $actualTokenClass){
            throw new MissedLexemeException("Expected \"$expectedTokenClass\" after \"$previousToken->bodyOfToken\", got \"$actualTokenClass\" instead\n");
        }
    }

}