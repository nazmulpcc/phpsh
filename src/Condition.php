<?php declare(strict_types=1);

namespace PhpSh;

class Condition
{
    /**
     * @var string
     */
    protected $lastVariable = '';

    /**
     * @var string[]
     */
    protected $fragments = [];

    /**
     * Initialize a condition
     * @param string|bool $expression
     * @return self
     */
    public static function create($expression = false) : self
    {
        $instance = new static();
        if ($expression) {
            return $instance->addFragment($expression);
        } else {
            return $instance;
        }
    }

    /**
     * @param string $variable
     * @return self
     */
    public function is(string $variable) : self
    {
        $variable = $this->safeVariable($variable);

        return $this->addFragment($variable);
    }

    /**
     * @param string $expression
     * @return self
     */
    public function equals(string $expression) : self
    {
        return $this->addFragment('-eq '. $expression);
    }

    /**
     * @param string $expression
     * @return self
     */
    public function notEquals(string $expression) : self
    {
        return $this->addFragment('-ne '. $expression);
    }

    /**
     * @param string $expression
     * @return self
     */
    public function greaterThan(string $expression) : self
    {
        return $this->addFragment('-gt '. $expression);
    }

    /**
     * @param string $expression
     * @return self
     */
    public function lessThan(string $expression) : self
    {
        return $this->addFragment('-lt '. $expression);
    }

    /**
     * @param string $expression
     * @return self
     */
    public function notLessThan(string $expression) : self
    {
        return $this->addFragment('-ge '. $expression);
    }

    /**
     * @param string $expression
     * @return self
     */
    public function notGreaterThan(string $expression) : self
    {
        return $this->addFragment('-le '. $expression);
    }

    /**
     * @return self
     */
    public function and() : self
    {
        return $this->addFragment('-a');
    }

    /**
     * @return self
     */
    public function or() : self
    {
        return $this->addFragment('-o');
    }

    /**
     * @param string $variable
     * @return self
     */
    public function isEmpty(string $variable) : self
    {
        $variable = $this->safeVariable($variable);

        return $this->addFragment('-z '. $variable);
    }

    /**
     * @param string $variable
     * @return self
     */
    public function isset(string $variable) : self
    {
        $variable = $this->removeDollarSign($variable);

        return $this->isEmpty(sprintf('{%s+x}', $variable));
    }

    /**
     * @param string $variable
     * @return self
     */
    public function isNotEmpty(string $variable) : self
    {
        $variable = $this->safeVariable($variable);

        return $this->addFragment('-n '. $variable);
    }

    /**
     * @param string $path
     * @return self
     */
    public function fileExists(string $path) : self
    {
        return $this->checkPath('f', $path);
    }

    /**
     * @param string $path
     * @return self
     * @see Condition::fileExists
     */
    public function isFile(string $path) : self
    {
        return $this->fileExists($path);
    }

    /**
     * @param $path
     * @return self
     */
    public function readable(string $path) : self
    {
        return $this->checkPath('r', $path);
    }

    /**
     * @param string $path
     * @return self
     */
    public function writable(string $path)
    {
        return $this->checkPath('w', $path);
    }

    /**
     * @param string $path
     * @return self
     */
    public function executable(string $path) : self
    {
        return $this->checkPath('x', $path);
    }

    /**
     * @param string $path
     * @return self
     */
    public function notEmptyFile(string $path) : self
    {
        return $this->checkPath('s', $path);
    }

    /**
     * @param string $path
     * @return self
     */
    public function pathExists(string $path) : self
    {
        return $this->checkPath('e', $path);
    }

    /**
     * @param string $path
     * @return self
     */
    public function isDir(string $path) : self
    {
        return $this->checkPath('d', $path);
    }

    /**
     * @param string $path
     * @return self
     */
    public function directoryExists(string $path) : self
    {
        return $this->isDir($path);
    }

    /**
     * @param string $operator
     * @param string $path
     * @return self
     */
    public function checkPath(string $operator, string $path) : self
    {
        return $this->addFragment("-{$operator} $path");
    }

    /**
     * Convert the Condition object to shell condition string
     * @return string
     */
    public function generate()
    {
        return implode(' ', $this->fragments);
    }

    public function __toString() : string
    {
        return $this->generate();
    }

    /**
     * @param $variable
     * @return string
     */
    protected function safeVariable($variable) : string
    {
        if ($variable[0] !== '$') {
            return '$'. $variable;
        }

        return $variable;
    }

    /**
     * Remove $ from a variable
     * @param $variable
     * @return string
     */
    protected function removeDollarSign($variable) : string
    {
        if ($variable[0] === '$') {
            return substr($variable, 1);
        }

        return $variable;
    }

    /**
     *
     * @param string $part
     * @return self
     */
    protected function addFragment(string $part) : self
    {
        $this->fragments[] = $part;

        return $this;
    }
}
