<?php

require_once "vendor/autoload.php";

$tokenizer = new \ClangPHP\Lexer\Tokenizer;

$code = <<<TEST
#define YOU_ARE_IN_MY_CASE "HELLO WORLD OF YOU"
int main() {
    printf(YOU_ARE_IN_MY_CASE);
    return 0;
}
TEST;

$start = $tokenizer->tokenize($code);

while (true) {
    echo $start->__toString() . "\t";
    try {
        $start = $start->next();
    } catch (\Exception $e) {
        break;
    }
}