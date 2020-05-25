<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2018/3/12
 * Time: 下午3:25
 */
namespace org;

use \think\facade\Log;

class Csv
{
    public $csv_array; //csv数组数据
    public $csv_str;  //csv文件数据
    private $basePath; //csv文件存放根目录

    public function __construct($csvData = [])
    {
        $this->csv_array = $csvData;
        $this->basePath  = defined("FILE_PATH") ? FILE_PATH : ROOT_PATH . 'public' . DS . 'static' . DS . 'file';
    }

    private function checkPath($path = '')
    {
        if (!is_dir($path)) {
            @mkdirs($path, 0777, true);
        }
    }


    /**
     * 导出
     * @param string $type
     * @return array
     */
    public function export($type = 'goods')
    {
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '参数丢失'
        ];
        try {
            if (!$type) {
                $type = 'goods';
            }
            $file_name = $type . "-csv-" . date("YmdHis", time()) . ".csv";
            $filePath  = $this->basePath . get_hash_dir($file_name);
            $this->checkPath($filePath);

            if (empty($this->csv_array)) {

                return $result;
            }
            $csvData = $this->csv_array;
            unset($this->csv_array);
            foreach ($csvData['header'] as $key => $v) {
                $csvData['header'][$key] = @iconv('UTF-8', 'GB2312//IGNORE', $v);
            }
            $fullFilePath = $filePath . $file_name;
            $file         = fopen($fullFilePath, "w");
            $limit        = 1000;
            $calc         = 0;
            fputcsv($file, $csvData['header']);
            unset($csvData['header']);

            //组装数据
            $bodyData = $csvData['body'];

            foreach ($bodyData as $k => $v) {
                $calc++;
                if ($limit == $calc) {
                    ob_flush();
                    flush();
                    $calc = 0;
                }
                foreach ($v as $t) {
                    $tempArr[] = @iconv('UTF-8', 'GB2312//IGNORE', $t);
                }
                fputcsv($file, $tempArr);
                unset($tempArr);
            }
            fclose($file);
        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();
            Log::record($e->getMessage(), 'error');
            return $result;
        }
        $result['status'] = true;
        $result['msg']    = error_code(10040,true);
        $result['data']   = [
            'file'     => $filePath,
            'filename' => $file_name,
            'filesize' => getRealSize(filesize($fullFilePath)),
        ];
        return $result;
    }

    /**
     * 导入
     * */
    public function import($path)
    {
        set_time_limit(0);
        $result = [
            'status' => false,
            'data'   => [],
            'msg'    => '未处理'
        ];
        try {
            if (!file_exists($path)) {
                $result['msg'] = '文件不存在';
                return $result;
            }

            $max_column = 1000;//最大行
            $max_byte  = 50 * 1024 * 1024;//最大字节 50M，一行数据不要超过50M
            $row        = 0;
            //读取文件
            $handle    = fopen($path, 'r');
            $dataArray = array();
            while ($data = fgetcsv($handle, $max_byte, ",")) {
                $row++;
                if ($row < $max_column) {
                    foreach ($data as $key => $val) {
                        if ($data[$key]) {
                            $data[$key] = @iconv('GB2312', 'UTF-8//IGNORE', $val);
                        }
                    }
                    $dataArray[] = $data;
                } else {
                    $result['msg'] = 'csv超过导入最大限制';
                    return $result;
                }
            }

            $result['msg']    = '数据获取成功';
            $result['status'] = true;
            $result['data']   = $dataArray;
        } catch (Exception $e) {
            $result['msg'] = $e->getMessage();
            Log::record($e->getMessage(), 'error');
        }
        return $result;
    }
}
