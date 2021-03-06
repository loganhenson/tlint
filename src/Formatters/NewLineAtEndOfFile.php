<?php

namespace Tighten\TLint\Formatters;

use PhpParser\Lexer;
use PhpParser\Parser;
use Tighten\TLint\BaseFormatter;

class NewLineAtEndOfFile extends BaseFormatter
{
    public const description = 'Applies a newline at the end of files.';

    public function format(Parser $parser, Lexer $lexer)
    {
        if (end($this->codeLines) ?? null !== '') {
            return $this->code . "\n";
        }

        return $this->code;
    }
}
