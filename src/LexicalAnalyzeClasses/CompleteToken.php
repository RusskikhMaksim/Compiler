<?php


class CompleteToken
{
    public string $bodyOfToken = "";
    public string $tokenClass = "";
    public int $startPositionInString;
    public int $endPositionInString;
    public int $NumOfStringInProgram;

    public function setParameters($body, $class,$startPos, $endPos, $numOfString){
        $this->bodyOfToken = $body;
        $this->tokenClass = $class;
        $this->startPositionInString = $startPos;
        $this->endPositionInString = $endPos;
        $this->NumOfStringInProgram = $numOfString;
    }
}

