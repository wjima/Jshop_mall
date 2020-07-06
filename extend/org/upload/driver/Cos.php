<?php
/**
 * 腾讯云cos上传
 */

namespace org\upload\driver;

if (is_file(__DIR__ . '/cos/vendor/autoload.php')) {
    require_once __DIR__ . '/cos/vendor/autoload.php';
}

use Qcloud\Cos\Client;

class Cos
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
        'secretId'  => '', //腾讯云secretId
        'secretKey' => '', //访问密钥
        'region'    => '', //访问域名
        'bucket'    => '', //空间名称
    ];

    /**
     * 构造函数，用于设置上传根路径
     * @param array $config FTP配置
     */
    public function __construct($config)
    {
        $this->config = $config;

        try {
            $this->cos = new Client(
                array(
                    'region'      => $this->config['region'],
                    'schema'      => 'https', //协议头部，默认为http
                    'credentials' => array(
                        'secretId'  => $this->config['secretId'],
                        'secretKey' => $this->config['secretKey'])));

        } catch (Exception $e) {
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
     * 检测上传目录(腾讯云上传时支持自动创建目录，直接返回)
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath)
    {
        return true;
    }

    /**
     * 创建文件夹 (腾讯云上传时支持自动创建目录，直接返回)
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

            $result = $this->cos->upload(
                $bucket = $this->config['bucket'], //格式：BucketName-APPID
                $key = $fileName,
                $body = fopen($filePath, 'rb')
            /*
            $options = array(
                'ACL' => 'string',
                'CacheControl' => 'string',
                'ContentDisposition' => 'string',
                'ContentEncoding' => 'string',
                'ContentLanguage' => 'string',
                'ContentLength' => integer,
                'ContentType' => 'string',
                'Expires' => 'string',
                'GrantFullControl' => 'string',
                'GrantRead' => 'string',
                'GrantWrite' => 'string',
                'Metadata' => array(
                    'string' => 'string',
                ),
                'ContentMD5' => 'string',
                'ServerSideEncryption' => 'string',
                'StorageClass' => 'string'
            )
            */
            );
            if (isset($result['Location'])) {
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
