<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EncryptionService
{
    /**
     * 加密數據
     * 
     * @param mixed $value
     * @return string|null
     */
    public function encrypt($value)
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        // 如果已經是字串且是 JSON 格式，則轉換後加密
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return Crypt::encryptString((string)$value);
    }

    /**
     * 解密數據
     * 
     * @param string|null $value
     * @param bool $isJson 是否需要解碼為 JSON
     * @return mixed
     */
    public function decrypt($value, $isJson = false)
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        try {
            $decrypted = Crypt::decryptString($value);
            
            if ($isJson) {
                return json_decode($decrypted, true);
            }
            
            return $decrypted;
        } catch (DecryptException $e) {
            // 如果解密失敗（可能是原始未加密數據），則回傳原值
            return $value;
        }
    }
}
