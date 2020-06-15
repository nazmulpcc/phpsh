<?php declare(strict_types=1);

namespace PhpSh;


class Condition
{
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
        if($expression){
            return $instance->addFragment($expression);
        }else{
            return $instance;
        }
    }

    /**
     * @param $expression
     * @return self
     */
    public function equals($expression) : self
    {
        return $this->addFragment('-eq '. $expression);
    }

    /**
     * @param $expression
     * @return static
     */
    public function notEquals($expression) : self
    {
        return $this->addFragment('-ne '. $expression);
    }

    /**
     * @param $expression
     * @return self
     */
    public function greaterThan($expression) : self
    {
        return $this->addFragment('-gt '. $expression);
    }

    /**
     * @param $expression
     * @return self
     */
    public function lessThan($expression) : self
    {
        return $this->addFragment('-lt '. $expression);
    }

    /**
     * @param $expression
     * @return self
     */
    public function notLessThan($expression) : self
    {
        return $this->addFragment('-ge '. $expression);
    }

    /**
     * @param $expression
     * @return self
     */
    public function notGreaterThan($expression) : self
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
     * @param $variable
     * @return self
     */
    public function isEmpty($variable) : self
    {
        return $this->addFragment(sprintf('-z $%s', $variable));
    }

    /**
     * @param $variable
     * @return self
     */
    public function isset($variable) : self
    {
        if ($variable[0] === '$'){
            $variable = substr($variable, 1);
        }
        return $this->isEmpty(sprintf('{%s+x}', $variable));
    }

    /**
     * @param $variable
     * @return self
     */
    public function isNotEmpty($variable) : self
    {
        return $this->addFragment(sprintf('-n $%s', $variable));
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
     * @param $path
     * @return static
     */
    public function directoryExists($path) : self
    {
        return $this->isDir($path);
    }

    /**
     * @param $operator
     * @param $path
     * @return self
     */
    public function checkPath($operator, $path) : self
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
     *
     * @param $part
     * @return self
     */
    protected function addFragment($part) : self
    {
        $this->fragments[] = $part;
        return $this;
    }
}
