<?php

namespace App\Utils;

class FileUtil
{
    /**
     * 获取文件本身的后缀
     * @param        $filename
     * @return mixed
     */
    public static function getFileExt($filename)
    {
        $fileinfo = pathinfo($filename);
        $fileext  = $fileinfo['extension'];
        return $fileext;
    }

    /**
     * 创建文件夹
     * mkdir 创建的目录默认权限是 0777 但被umask限制（通常是 0022），实际结果是 0755
     * @param  string $path
     * @param  int    $permissions
     * @param  bool   $recursive
     * @return void
     */
    public static function mkdir(string $path, int $permissions = 0777, bool $recursive = true)
    {
        if (is_dir($path) === false) {
            $path = dirname($path);
        }
        mkdir($path, $permissions, $recursive);
        chmod($path, $permissions);
        // 当前层的上一层也需要修改权限
        chmod(dirname($path), $permissions);
    }

    /**
     * 获取文件大小
     * @param               $file
     * @param  bool         $unit
     * @return float|string
     * @throws \Exception
     */
    public static function getFileSize($file, $unit = true)
    {
        if (!file_exists($file)) {
            throw new \Exception("File does not exist path:{$file}");
        }
        $byte = filesize($file);
        $KB   = 1024;
        $MB   = 1024 * $KB;
        $GB   = 1024 * $MB;
        $TB   = 1024 * $GB;
        if ($unit) {
            if ($byte < $KB) {
                return $byte . 'B';
            } elseif ($byte < $MB) {
                return round($byte / $KB, 2) . 'KB';
            } elseif ($byte < $GB) {
                return round($byte / $MB, 2) . 'MB';
            } elseif ($byte < $TB) {
                return round($byte / $GB, 2) . 'GB';
            }
            return round($byte / $TB, 2) . 'TB';
        }
        return round($byte / $KB, 2);
    }
}
