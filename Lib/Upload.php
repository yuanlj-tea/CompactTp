<?php
/**
 * upload
 * Enter description here ...
 * @author yaojw
 *
 */
namespace Framework\Lib;

class Upload
{

    public $type_maping = array(1 => 'image/gif', 2 => 'image/jpeg', 3 => 'image/png', 4 => 'image/jpg');


    public function uploadImg($upload, $dir)
    {
        if (!$this->make_dir($dir)) {
            /* 创建目录失败 */
            return false;
        }
        if (!in_array($upload['type'], $this->type_maping)) {
            return false;
        }
        $filename = $this->random_filename() . substr($upload['name'], strpos($upload['name'], '.'));
        if ($this->move_upload_file($upload['tmp_name'], $dir . $filename)) {
            return $filename;
        } else {
            return false;
        }
    }

    public function move_upload_file($file_name, $target_name = '')
    {
        if (move_uploaded_file($file_name, $target_name)) {
            @chmod($target_name, 0755);
            return true;
        } elseif (copy($file_name, $target_name)) {
            @chmod($target_name, 0755);
            return true;
        }
        return false;
    }

    public function random_filename()
    {
        $str = '';
        for ($i = 0; $i < 9; $i++) {
            $str .= mt_rand(0, 9);
        }
        return time() . $str;
    }

    /**
     * 检查目标文件夹是否存在，如果不存在则自动创建该目录
     *
     * @access      public
     * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
     *
     * @return      bool
     */
    public function make_dir($folder)
    {
        $reval = false;
        if (!file_exists($folder)) {
            /* 如果目录不存在则尝试创建该目录 */
            @umask(0);
            /* 将目录路径拆分成数组 */
            preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);
            /* 如果第一个字符为/则当作物理路径处理 */
            $base = ($atmp[0][0] == '/') ? '/' : '';
            /* 遍历包含路径信息的数组 */
            foreach ($atmp[1] as $val) {
                if ('' != $val) {
                    $base .= $val;
                    if ('..' == $val || '.' == $val) {/* 如果目录为.或者..则直接补/继续下一个循环 */
                        $base .= '/';
                        continue;
                    }
                } else {
                    continue;
                }
                $base .= '/';
                if (!file_exists($base)) {
                    /* 尝试创建目录，如果创建失败则继续循环 */
                    if (@mkdir(rtrim($base, '/'), 0777)) {
                        @chmod($base, 0777);
                        $reval = true;
                    }
                }
            }
        } else {
            /* 路径已经存在。返回该路径是不是一个目录 */
            $reval = is_dir($folder);
        }
        return $reval;
    }


}