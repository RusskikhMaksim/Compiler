<?php
require_once './Lexer.php';
require_once './Parser.php';
require_once 'src/SymbolTableClasses/SymbolTableClass.php';
require_once 'src/ExceptionsClasses/MissedLParenException.php';
require_once 'src/ExceptionsClasses/MissedRParenException.php';
require_once 'src/ExceptionsClasses/RedifinationException.php';
require_once 'src/ExceptionsClasses/WithoutDifinationException.php';
require_once 'src/ExceptionsClasses/UnexpectedLParenException.php';
require_once 'src/ExceptionsClasses/UnexpectedRParenException.php';
require_once 'src/ExceptionsClasses/MissedSqLParenException.php';
require_once 'src/ExceptionsClasses/MissedSqRParenException.php';
require_once 'src/ExceptionsClasses/UnexpectedReturnValueException.php';
require_once 'src/ExceptionsClasses/MissedLexemeException.php';
require_once 'src/astClasses/SyntaxErrorHandler.php';
require_once 'src/astClasses/AstNode.php';
require_once 'src/astClasses/AstClass.php';
require_once './src/LexicalAnalyzeClasses/CompleteToken.php';
require_once './src/LexicalAnalyzeClasses/LexicalAnalyze.php';
require_once './src/LexicalAnalyzeClasses/TokenFormation.php';
require_once 'src/astClasses/AstPreprocessorDirectiveClass.php';
require_once 'src/astClasses/AstRootClass.php';
require_once 'src/astClasses/AstLibFuncClass.php';
require_once 'src/astClasses/InstructionKeyWordClass.php';
require_once 'src/astClasses/AstDeclarationClass.php';
require_once 'src/astClasses/AstIfClass.php';
require_once 'src/astClasses/AstWhileClass.php';
require_once 'src/astClasses/AstAssigmentClass.php';





