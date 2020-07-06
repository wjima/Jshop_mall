<?php
/**
 * 阿里云oss上传
 */

namespace org\upload\driver;

if (is_file(__DIR__ . '/aliyun/autoload.php')) {
    require_once __DIR__ . '/aliyun/autoload.php';
}


use OSS\OssClient;
use OSS\Core\OssException;


class Aliyun
{
    /**
     * 上传文件根目录
     * @var string
     */
    private $rootPath;

    /**
     * 上传错误信息
     * @var string
     */
    private $error = '';

    private $config = [
        'accessKeyId'     => '', //阿里云accesskeyid，用户AccessKey控制台地址：https://usercenter.console.aliyun.com/#/manage/ak
        'accessKeySecret' => '', //访问密钥
        'endpoint'        => '', //访问域名
        'bucket'          => '', //空间名称
    ];

    /**
     * 构造函数，用于设置上传根路径
     * @param array $config FTP配置
     */
    public function __construct($config)
    {
        $this->config = $config;

        try {
            $this->aliyun = new OssClient($this->config['accessKeyId'], $this->config['accessKeySecret'], $this->config['endpoint'], false);
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return null;
        }
    }

    /**
     * 检测上传根目录(七牛上传时支持自动创建目录，直接返回)
     * @param string $rootpath 根目录
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath($rootpath)
    {
        $this->rootPath = trim($rootpath, './') . '/';
        return true;
    }

    /**
     * 检测上传目录(阿里云上传时支持自动创建目录，直接返回)
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath)
    {
        return true;
    }

    /**
     * 创建文件夹 (阿里云上传时支持自动创建目录，直接返回)
     * @param  string $savepath 目录名称
     * @return boolean          true-创建成功，false-创建失败
     */
    public function mkdir($savepath)
    {
        return true;
    }

    /**
     * 保存指定文件
     * @param  array $file 保存的文件信息
     * @param  boolean $replace 同名文件是否覆盖
     * @return boolean          保存状态，true-成功，false-失败
     */
    public function save(&$file, $replace = true)
    {
        $filePath = $file['tmp_name'];

        try {

            $fileName = substr($file['savepath'], 1) . $file['savename'];

            $result = $this->aliyun->uploadFile($this->config['oss_bucket'], $fileName, $filePath);
            if (isset($result['info']['url'])) {
                return true;
            } else {
                $this->error = error_code(10035,true);
            }
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError()
    {
        return $this->error;
    }
}
