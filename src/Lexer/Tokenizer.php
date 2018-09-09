<?php
/**
 * ClangPHP Project
 * 
 * @author Tianle Xu<xtl@xtlsoft.top>
 * @package ClangPHP
 * @category languages
 */

namespace ClangPHP\Lexer;

class Tokenizer {

    public static $separators = [',', ';', '+', '-', '*', '/', '&', '>', '<', '=', '^', '%', '[', ']', '(', ')', '{', '}', '~', '|', ':'];
    public static $exceptions = ['>=', '<=', '==', '++', '--', '>>', '<<', '+=', '-=', '*=', '/=', '%='];
    public static $definitions = [
        '__IN_CLANG_PHP__' => "true"
    ];
    public static $blanks = ["\t", "\n", "\r", " "];
    public static $quotes = ['"', "'", '`'];

    public function tokenize(string $code): \ClangPHP\Lexer\Tokenizer\Token {

        $definitions = self::$definitions;
        $code = str_split($code);
        $curr = "";
        $startnode = new \ClangPHP\Lexer\Tokenizer\Token;
        $currnode = $startnode;

        $line = 1;
        $col = 1;
        $k = 0;
        $inquote = '';

        for ($k = 0; $k < count($code); ++ $k) {
            ++ $col;
            $v = $code[$k];
            if ($v === "\n") {
                $line ++;
                $col = 0;
            }
            if ($currnode->getLine() === 0) {
                $currnode->setPosition($line, $col);
            }
            if ($v === '#') {
                $callName = '';
                $argument = [''];
                $stage = 0;
                $in_pre_quote = '';
                while ($v !== "\n" && $k < count($code)) {
                    if ($in_pre_quote === '"') {
                        if ($v === '"' && $code[$k-1] !== '\\') {
                            $argument[$stage] .= $v;
                            $in_pre_quote = '';
                            $k ++;
                            $v = @$code[$k];
                            $stage ++;
                            $argument[$stage] = '';
                            continue;
                        } else {
                            $argument[$stage] .= $v;
                            $k ++;
                            $v = @$code[$k];
                            continue;
                        }
                    }
                    if ($in_pre_quote === '<') {
                        if ($v === '>') {
                            $argument[$stage] .= $v;
                            $in_pre_quote = '';
                            $k ++;
                            $v = @$code[$k];
                            $stage ++;
                            $argument[$stage] = '';
                            continue;
                        } else {
                            $argument[$stage] .= $v;
                            $k ++;
                            $v = @$code[$k];
                            continue;
                        }
                    }
                    if ($v === '"' && $v === '<') {
                        $argument[$stage] .= $v;
                        $in_pre_quote = $v;
                    } else {
                        if ($v === ' ' || $v === "\t") {
                            $stage ++;
                            $argument[$stage] = '';
                        } else {
                            $argument[$stage] .= $v;
                        }
                    }
                    $k ++;
                    $v = @$code[$k];
                }
                $callName = strtolower($argument[0]);
                $argument = array_slice($argument, 1);
                switch ($callName) {
                    case "#include":
                        // Do Including
                        break;
                    case "#define":
                        $arg1 = trim($argument[0]);
                        $arg2 = join(" ", array_slice($argument, 1));
                        $definitions[$arg1] = $arg2;
                        break;
                }
                continue;
            }
            if (in_array($v, self::$quotes)) {
                if ($inquote === '') {
                    $inquote = $v;
                    $currnode->setData($v);
                    continue;
                }
                if ($code[$k-1] !== '\\') {
                    $inquote = '';
                    $currnode->setData($currnode->__toString() . $v);
                    $node = new \ClangPHP\Lexer\Tokenizer\Token;
                    $currnode->next($node);
                    $node->previous($currnode);
                    if (isset($definitions[$currnode->__toString()])) {
                        $currnode->setData($definitions[$currnode->__toString()]);
                    }
                    $currnode = $node;
                    continue;
                }
            }
            if ($inquote !== '') {
                $currnode->setData($currnode->__toString() . $v);
                continue;
            }
            if (in_array($v, self::$blanks)) {
                if ($currnode->__toString() === "") {
                    continue;
                } else {
                    $node = new \ClangPHP\Lexer\Tokenizer\Token;
                    $currnode->next($node);
                    $node->previous($currnode);
                    $node->setPosition($line, $col);
                    if (isset($definitions[$currnode->__toString()])) {
                        $currnode->setData($definitions[$currnode->__toString()]);
                    }
                    $currnode = $node;
                    continue;
                }
            }
            if (in_array($v, self::$separators)) {
                if (in_array($code[$k-1].$v, self::$exceptions)) {
                    $currnode->previous()->setData($code[$k-1].$v);
                    $currnode->setPosition(0, 0);
                    continue;
                }
                if ($currnode->__toString() !== "") {
                    $node = new \ClangPHP\Lexer\Tokenizer\Token;
                    $currnode->next($node);
                    $node->previous($currnode);
                    $node->setPosition($line, $col);
                    if (isset($definitions[$currnode->__toString()])) {
                        $currnode->setData($definitions[$currnode->__toString()]);
                    }
                    $currnode = $node;
                }
                $currnode->setData($v);
                $node = new \ClangPHP\Lexer\Tokenizer\Token;
                $currnode->next($node);
                $node->previous($currnode);
                if (isset($definitions[$currnode->__toString()])) {
                    $currnode->setData($definitions[$currnode->__toString()]);    
                }
                $currnode = $node;
                continue;
            }
            $currnode->setData($currnode->__toString() . $v);
        }

        return $startnode;

    }

}