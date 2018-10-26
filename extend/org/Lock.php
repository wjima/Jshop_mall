<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/30
 * Time: 上午11:00
 */
namespace org;

class Lock{

    private $lockpath = '';//锁文件路径

    private $lock = '';//锁文件

    public function init($lock_name)
    {
        if($this->lockpath){
            $this->lock = $this->lockpath.'.'.$lock_name.'.lock';
        }else{
            $this->lock = ROOT_PATH.DS.'runtime'.DS.$lock_name.'.lock';
        }
        return $this;
    }

    private function lockfile()
    {
        return $this->lock;
    }//End Function


    /*
     * 写入lockfile文件
     * @access private
     * @return boolean
     */
    private function put_lockfile($content='')
    {
        if(!$content){
            $content = microtime();
        }
        return file_put_contents($this->lockfile(), $content);

    }//End Function

    /*
     * 写入lockfile文件
     * @access private
     * @return string
     */
    public function get_lockfile()
    {
        return file_get_contents($this->lockfile());
    }//End Function

    /*
     * 检查是否有lock文件
     * @access public
     * @return boolean
     */
    public function lockfile_exists()
    {
        return file_exists($this->lockfile());
    }//End Function

    /*
     * 写入锁文件
     * @access public
     * @return string
     */
    public function write_lock_file(){
        $content = microtime();
        if($this->put_lockfile($content)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 锁文件删除
     * @return bool
     */
    public function unlock()
    {
        return @unlink($this->lockfile());
    }

}