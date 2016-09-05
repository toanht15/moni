<?php

class Cipher
{
	/**
	 * 暗号化用Key
	 * @var string
	 */
	public $baseKey = 'asdfghjkl;:]qwertyuip@-09877612345678oiutreasdfghjkl';

	/**
	 * 暗号化用個別Key
	 * @var string
	 */
	public $soltKey = '';

	/**
	 * baseKeyの設定
	 * @param string $baseKey
	 */
	public function setBaseKey($baseKey = '')
	{
		$this->baseKey = $baseKey;
	}

	/**
	 * 暗号化
	 * @param array|string $data
	 * @param string $key
	 * @return string
	 */
	public function encode($data = [], $key = '')
	{
		$key = $this->createHashKey($key);
		$plaintext = serialize($data);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$ciphertext = mcrypt_encrypt(
			MCRYPT_RIJNDAEL_128,
			$key,
			$plaintext,
			MCRYPT_MODE_CBC,
			$iv
		);
		$ciphertext = $iv . $ciphertext;
		$ciphertext_base64 = base64_encode($ciphertext);
		return $ciphertext_base64;
	}

	/**
	 * 複合化
	 * @param array|string $data
	 * @param string $key
	 * @return string|array
	 */
	public function decode($text = '', $key = '')
	{
		$key = $this->createHashKey($key);
		$ciphertext_dec = base64_decode($text);
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv_dec = substr($ciphertext_dec, 0, $iv_size);
		$ciphertext_dec = substr($ciphertext_dec, $iv_size);
		$plaintext_dec = mcrypt_decrypt(
			MCRYPT_RIJNDAEL_128,
			$key,
			$ciphertext_dec,
			MCRYPT_MODE_CBC,
			$iv_dec
		);
		$result = unserialize($plaintext_dec);
		if ($result) {
			return $result;
		}
		return $plaintext_dec;
	}

	/**
	 * キーの作成
	 * @param string $key
	 * @return string
	 */
	private function createHashKey($key = '')
	{
		return pack('H*', $this->baseKey . $key);
	}

}