<?php

namespace App\Utils;

class AesDynamicUtil
{
    /**
     * 生成 16 字节随机数据（对应 32 位十六进制字符）
     * $bytes = random_bytes(16);
     * $hex = bin2hex($bytes);
     */
    public const KEY = '632800735d06bfbb5ae344bcbdabb963';

    public const CIPHER_METHOD = 'aes-256-cbc';

    /**
     * 生成UUIDv4格式的请求ID
     * @return string
     * @throws \Random\RandomException
     */
    public static function generateRequestId()
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // 版本4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // 变体1
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * 加密
     * @param         $string
     * @param         $requestId
     * @param         $key
     * @return string
     */
    public static function encryptRaw($string, $requestId, $key = AesDynamicUtil::KEY)
    {
        $string    = gzencode($string);
        $key       = self::generateKey($requestId, $key);
        $iv        = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER_METHOD));
        $encrypted = openssl_encrypt($string, self::CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
        return $iv . $encrypted;
    }

    /**
     * 解密
     * @param               $encryptedData
     * @param               $requestId
     * @param               $key
     * @return false|string
     */
    public static function decryptRaw($encryptedData, $requestId, $key = AesDynamicUtil::KEY)
    {
        $key        = self::generateKey($requestId, $key);
        $ivLength   = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $iv         = substr($encryptedData, 0, $ivLength);
        $cipherText = substr($encryptedData, $ivLength);
        $decodeData = openssl_decrypt($cipherText, self::CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
        //        LogUtil::info("RequestId ".$requestId);
        //        LogUtil::info("Key ".bin2hex($key));
        //        LogUtil::info("Iv ".bin2hex($iv));
        return gzdecode($decodeData);
    }

    /**
     * 加密
     * @param         $string
     * @param         $requestId
     * @param         $key
     * @return string
     */
    public static function encryptBase64($string, $requestId, $key = AesDynamicUtil::KEY)
    {
        $string    = gzencode($string);
        $key       = self::generateKey($requestId, $key);
        $iv        = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::CIPHER_METHOD));
        $encrypted = openssl_encrypt($string, self::CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * 解密
     * @param               $encryptedData
     * @param               $requestId
     * @param               $key
     * @return false|string
     */
    public static function decryptBase64($encryptedData, $requestId, $key = AesDynamicUtil::KEY)
    {
        $encryptedData = base64_decode($encryptedData);
        $ivLength      = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $iv            = substr($encryptedData, 0, $ivLength);
        $cipherText    = substr($encryptedData, $ivLength);
        $key           = self::generateKey($requestId, $key);
        $decodeData    = openssl_decrypt($cipherText, self::CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
        //        LogUtil::info("RequestId ".$requestId);
        //        LogUtil::info("Key ".bin2hex($key));
        //        LogUtil::info("Iv ".bin2hex($iv));
        return gzdecode($decodeData);
    }

    /**
     * 生成动态密钥（服务端和客户端使用相同算法）
     * @param         $requestId
     * @param  mixed  $key
     * @return string
     */
    private static function generateKey($requestId, $key = AesDynamicUtil::KEY)
    {
        // 使用HMAC从主密钥和请求ID派生密钥
        $cleanId = str_replace('-', '', $requestId);
        return hash_hmac('sha256', hex2bin($cleanId), $key, true);// 如果binary是false,则使用的时候需要 hex2bin()
    }
}
