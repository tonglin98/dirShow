<?php
namespace DirShow\tool;

use Cassandra\Varint;
use DirShow\exception\NotDirException;

class DirShow
{
    /**
     * need show dir
     * @var string $dir
     */
    private $dir;

    private $filter;

    /**
     * DirShow constructor.
     * @param string $dir
     * @throws NotDirException
     */
    public function __construct($dir)
    {
        if (!is_dir($dir)){
            throw new NotDirException('传递的目录路径错误');
        }

        $this->dir = $dir;
        $this->filter = false;
    }

    /**
     * 如果设置此项, 该目录下的所有文件都会经过此回调
     * @param callable $call
     * @return DirShow
     */
    public function setFilter(callable $call)
    {
        $this->filter = $call;
        return $this;
    }

    /**
     * read dir
     * @param $dir
     * @return array
     */
    private function readDir($dir)
    {
        $dirContent = scandir($dir);
        $res = [];

        if ($dirContent === false) {
            return $res;
        }

        foreach ($dirContent as $item) {

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_file($path)) {

                $call = $this->filter;

                // no filter
                if ($call === false) {

                    $res[] = $path;
                }else{
                    $filterRes = $call($path);

                    if ($filterRes === false) {
                        continue;
                    }

                    $res[] = $filterRes;
                }

            }elseif (is_dir($path) && !in_array($item,['.','..'])) {

                $res[$item] = $this->readDir($path);
            }else{
                // ...
            }
        }

        return $res;
    }

    /**
     * 显示目录下所有文件
     * @return array
     */
    public function show()
    {
        return $this->readDir($this->dir);
    }
}
