<?php
namespace Framework\Controller;

class Error extends Basic
{
    public $exception;

    public function __construct(\Exception $e)
    {
        $this->exception=$e;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return \Framework\Core\Exception::text($this->exception);
    }

    public function index()
    {
        $this->httpStatus($this->exception->getCode());
        $this->web();
    }

    public function web()
    {
        echo '<pre>';
        echo PHP_EOL.$this->getText().PHP_EOL.$this->exception->getTraceAsString().PHP_EOL;
        echo '</pre>';
    }

    public function cli()
    {
        echo PHP_EOL.$this->getText().PHP_EOL;
    }

    /**
     * @param $code
     * @return bool
     */
    public function httpStatus($code)
    {
        return \Framework\Lib\Response::httpStatus($code);
    }
}