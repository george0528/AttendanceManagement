<?php

namespace App\Services;

class AuthService
{
  // 試行回数の限界
  protected $maxAttempts = 3;

  // IPアドレスがロックされているか
  public function is_login_lock($login_key, $ip_address)
  {
    $cache_key = $this->generateKey($login_key, $ip_address);
    $cache_value = cache()->get($cache_key);

    if($cache_value) {
      if($cache_value > $this->maxAttempts) {
        return true;
      }
    }

    return false;
  }

  // ログインに成功
  public function login_success($login_key, $ip_address)
  {
    $cache_key = $this->generateKey($login_key, $ip_address);
    
    return cache()->forget($cache_key);
  }

  // ログインに失敗
  public function login_failed($login_key, $ip_address)
  {
    $cache_key = $this->generateKey($login_key, $ip_address);

    return cache()->increment($cache_key);
  }

  // キャッシュのkeyの文字列を作る
  public function generateKey($login_key, $ip_address)
  {
    return $login_key.':'.$ip_address;
  }
}