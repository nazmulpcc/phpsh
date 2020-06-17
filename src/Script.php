<?php declare(strict_types=1);

namespace PhpSh;

class Script
{
    /**
     * @var string[]
     */
    protected $fragments = [];

    /**
     * @var int
     */
    protected $nested = 0;

    /**
     * Add a new command line
     * @param string|static $expression
     * @return self
     */
    public function line($expression) : self
    {
        $expression = (string) $expression;

        return $this->addFragment($expression);
    }

    /**
     * Set the value of a variable
     * @param string $variable
     * @param string $expression
     * @return self
     */
    public function set(string $variable, string $expression) : self
    {
        return $this->line(sprintf(
            '%s=%s',
            $variable,
            is_numeric($expression) ? $expression : static::doubleQuote($expression)
        ));
    }

    /**
     * Construct an if condition
     * @param string|Condition $condition
     * @param callable $callable A callable that receives a new Script instance as argument
     * @param bool $double
     * @param string $tag
     * @return self
     */
    public function if($condition, callable $callable, bool $double = false, string $tag = 'if') : self
    {
        $script = $this->newNestedScript($callable);

        return $this
            ->line(implode(' ', [
                $tag,
                $double ? '[[ ' : '[ ',
                $condition,
                $double ? ' ]]' : ' ]',
                '; then',
            ]))
            ->line($script);
    }

    /**
     * @param callable $callable
     * @return self
     */
    public function else(callable $callable) : self
    {
        $script = $this->newNestedScript($callable);

        return $this
            ->line('else')
            ->line($script)
            ->fi();
    }

    /**
     * @param string|Condition $condition
     * @param callable $callable
     * @param bool $double
     * @return self
     */
    public function elseif($condition, callable $callable, bool $double = true) : self
    {
        return $this->if($condition, $callable, $double, 'elif');
    }

    /**
     * @see Script::fi
     */
    public function endif() : self
    {
        return $this->fi();
    }

    /**
     * Finish up an if block
     * @return self
     */
    public function fi() : self
    {
        return $this->line('fi');
    }

    /**
     * @param string $variable
     * @param callable $callable
     * @return self
     */
    public function switch(string $variable, callable $callable) : self
    {
        return $this
            ->line(sprintf('case $%s in', $variable))
            ->line($this->newNestedScript($callable))
            ->line('esac');
    }

    /**
     * @param string $pattern
     * @param callable $callable
     * @return self
     */
    public function case(string $pattern, callable $callable) : self
    {
        return $this
            ->line("$pattern)")
            ->line($this->newNestedScript($callable))
            ->line(';;');
    }

    /**
     * @param string|Condition $condition
     * @param callable $callable
     * @return self
     */
    public function while($condition, callable $callable) : self
    {
        return $this
            ->line(sprintf('while [ %s ]; do', (string) $condition))
            ->line($this->newNestedScript($callable))
            ->line('done');
    }

    /**
     * @param string $expression
     * @return self
     */
    public function break(string  $expression = '') : self
    {
        return $this->line('break '. $expression);
    }

    /**
     * @param string $expression
     * @return self
     */
    public function continue(string $expression = '') : self
    {
        return $this->line('continue '. $expression);
    }

    /**
     * @param $variable
     * @param $count
     * @return self
     */
    public function increment(string $variable, int $count = 1) : self
    {
        return $this->line(sprintf('let %s+=%d', $variable, $count));
    }

    /**
     * @param $variable
     * @param $count
     * @return self
     */
    public function decrement(string $variable, int $count = 1) : self
    {
        return $this->line(sprintf('let %s-=%d', $variable, $count));
    }

    /**
     * @param string $expression
     * @param bool $newline
     * @return self
     */
    public function echo(string $expression, bool $newline = false) : self
    {
        return $this->line(sprintf(
            'echo %s %s',
            $newline ? '' : '-n',
            $expression
        ));
    }

    /**
     * @param string $expression
     * @param array|bool $arguments
     * @return self
     */
    public function printf(string $expression, $arguments = false) : self
    {
        return $this->line(implode(' ', [
            'printf',
            static::doubleQuote($expression),
            $arguments ? static::doubleQuote(implode('" "', $arguments)) : '',
        ]));
    }

    /**
     * Surround an expression with double quotes
     * @param string $expression
     * @return string
     */
    public static function doubleQuote(string $expression) : string
    {
        return sprintf('"%s"', $expression);
    }

    /**
     * Generates the resulting shell script
     * @return string
     */
    public function generate()
    {
        $result = '';
        $length = count($this->fragments);
        for ($i = 0; $i < $length; $i++) {
            $result .= str_pad('', $this->nested, "\t");
            $result .= $this->fragments[$i];
            if ($i < $length - 1) {
                $result .= PHP_EOL;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->generate();
    }

    /**
     * Add a new shell fragment
     * @param string $line
     * @return self
     */
    protected function addFragment(string $line) : self
    {
        $this->fragments[] = $line;

        return $this;
    }

    /**
     * Create a new nested script fragment
     * @param callable $callable
     * @return $this
     */
    protected function newNestedScript(callable $callable) : Script
    {
        $script = new Script();
        $script->nested = $this->nested + 1;
        call_user_func_array($callable, [&$script]);

        return $script;
    }
}
