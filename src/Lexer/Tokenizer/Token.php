<?php
/**
 * ClangPHP Project
 * 
 * @author Tianle Xu<xtl@xtlsoft.top>
 * @package ClangPHP
 * @category languages
 */

namespace ClangPHP\Lexer\Tokenizer;

class Token {
    
    /**
     * The previous token.
     * 
     * @var $previous
     */
    protected $previous = null;

    /**
     * The next token.
     * 
     * @var $next
     */
    protected $next = null;

    /**
     * The data
     *
     * @var string
     */
    protected $data = '';

    /**
     * Line
     *
     * @var integer
     */
    protected $line = 0;

    /**
     * Column
     *
     * @var integer
     */
    protected $column = 0;

    /**
     * Get or set the previous token
     * 
     * @param null|\ClangPHP\Lexer\Tokenizer\Token $token
     * @return \ClangPHP\Lexer\Tokenizer\Token
     */
    public function previous($token = null): Token {
        
        if ($token !== null && $token instanceof Token) {
            $this->previous = $token;
        }

        return $this->previous;

    }

    /**
     * Get or set the next token
     * 
     * @param null|\ClangPHP\Lexer\Tokenizer\Token $token
     * @return \ClangPHP\Lexer\Tokenizer\Token
     */
    public function next($token = null): Token {
        
        if ($token !== null && $token instanceof Token) {
            $this->next = $token;
        }

        if ($this->next === null) {
            throw new \Exception("Cannot be null");
        }

        return $this->next;

    }

    /**
     * Set the data
     *
     * @return void
     */
    public function setData(string $data) {
        $this->data = $data;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString(): string {
        return $this->data;
    }

    /**
     * Set the position
     *
     * @param integer $line
     * @param integer $column
     * @return void
     */
    public function setPosition(int $line, int $column) {
        $this->line = $line;
        $this->column = $column;
    }

    /**
     * The line number
     *
     * @return integer
     */
    public function getLine(): int {
        return $this->line;
    }

    /**
     * The column number
     *
     * @return integer
     */
    public function getColumn(): int {
        return $this->column;
    }

}