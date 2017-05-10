<?php
namespace Framework\View;

use Framework\Core\App;

class Basic extends \Framework\Core\Result
{
    public $options=array(
        'source_dir'=>null,
        'source_ext'=>'php',
    );

    public function __construct(array $options=array())
    {
        $this->options=App::getOption('view',$options)+$this->options;

        $this->options['source_dir'] or $this->options['source_dir']=ROOT_PATH.'View';
    }

    /**
     * @param $name
     * @param null $ext
     */
    public function render($name,$ext=null)
    {
        $name=$this->getSourceFile($name,$ext);
        \extract($this->resultGet(),EXTR_SKIP);

        include $name;
    }

    /**
     * @param $name
     * @param null $ext
     * @return string
     */
    public function fetch($name,$ext=null)
    {
        \ob_start();
        $this->render($name,$ext);
        return \ob_get_clean();
    }

    /**
     * @param $name
     * @param $ext
     * @return string
     * @throws \Framework\Core\Exception
     */
    public function getSourceFile($name,$ext)
    {
        $ext or $ext=$this->options['source_ext'];
        $name = $this->options['source_dir'].DS.$name.'.'.$ext;
        if(\is_file($name)){
            return $name;
        }
        throw new \Framework\Core\Exception('View file'.$name.'not found');
    }
}