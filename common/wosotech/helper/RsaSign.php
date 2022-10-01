<?php
namespace common\wosotech\helper;

/*
$data='merchantId=898310054114016&merchantUserId=00000000001&userId=000121154626&merOrderId=1476260102&amount=1&notifyUrl=http://www.chinaums.com';
$private='MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALjn5GMMRHSHinMILa9mRYlP7ebygP5IZIwTeykpRnSc5sW7RrqE1opiXGi0pCQ2fBuK8VPOWBxYkqt4g29SJ05Ts78HUq2yl4t3ZgWhDZVsoG3OaQ2Sz8ArmfT3VxsvLF3v6ZlhHBNfRAmJbukNBSio58NHGhLoem0l0wTuwf+LAgMBAAECgYEArkqvRgnXMPxeLaYE4jOF0jPMbQgjPQ1h8YOfcSId7mfDQ5kOx1vVmqWys2Oq4ROWkqO6bKOw/C8lOYokYjdPIfC4HWXPFNvPFWs6JptNb90+Os6RHK8LN8ut/F0pbTWhC3Y7tgBBdY8SoRhHSmvk5raih8tUTOrSt7TSMz6s/fECQQD58Sr3/NcrChIv8Vlc+Ks0LR0z4H0EFgsYqUoV1ziZBq7e4WNbB5Pi9cYQGJ/HGfctYnEI2g5prwwk0283Th2zAkEAvWMwjHN8bwc74RhHAxCv8ZpfXZye9i/MJPrrdRIfqm5uwfd0oN0Fpt1TEph8H876WgkiVHp9SDKkkQ0C57WayQJAXfKyzggx8LGWaIL1riaiY7hZc7h8BV8ryJdJi7AcTBjg/lIGAJ92jScIzeATnsk5ycto5YThSgRMMkNvWIB6VQJAc6D0yjvUVEF5aLQW3yM4GO2knhX649pI7KcaTQ70sGzeSKTZ20E2qytkBe19kzoelgwPnr5ucT8iRMFJ7chuKQJATQgFVhZnVDpFcivxOEWOLsIDB3Ibml3ccFM6teZNVTUTiLoF1aPHadHL8k8DVZG6BSLmgfPDTEgDwxMIrluEew==';
$public='MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC45+RjDER0h4pzCC2vZkWJT+3m8oD+SGSME3spKUZ0nObFu0a6hNaKYlxotKQkNnwbivFTzlgcWJKreINvUidOU7O/B1KtspeLd2YFoQ2VbKBtzmkNks/AK5n091cbLyxd7+mZYRwTX0QJiW7pDQUoqOfDRxoS6HptJdME7sH/iwIDAQAB';
$privateKey=$rsa->privateKey($private);
$sign=$rsa->sign($data, $privateKey);
var_dump($sign);
$publicKey=$rsa->publicKey($public);
$vrify=$rsa->verifyBase64($data, $sign, $publicKey);
var_dump($vrify);
*/

// if php version < 5.4.0
if (! function_exists ( 'hex2bin' )) {
	function hex2bin($hex) {
		$n = strlen ( $hex );
		$bin = "";
		$i = 0;
		while ( $i < $n ) {
			$a = substr ( $hex, $i, 2 );
			$c = pack ( "H*", $a );
			if ($i == 0) {
				$bin = $c;
			} else {
				$bin .= $c;
			}
			$i += 2;
		}
		return $bin;
	}
}

class RsaSign {
	/**
	 * 签名数据
	 *
	 * @param string $data
	 *        	要签名的数据
	 * @param string $private
	 *        	私钥文件
	 * @return string 签名的16进制数据
	 */
	function sign($data, $private) {
		$p = openssl_pkey_get_private ( $private );
		openssl_sign ( $data, $signature, $p );
		openssl_free_key ( $p );
		return base64_encode($signature );
	}

	/**
	 * 验签
	 *
	 * @param string $data
	 * @param string $sign
	 * @param string $pem
	 * @return bool hexSting(16进制签名)验签
	 */
	function verify($data, $sign, $public) {
		$p = openssl_pkey_get_public ( $public ) ;
		var_dump($p);
		$verify = openssl_verify ( $data, hex2bin ( $sign ), $p );
		openssl_free_key ( $p );
		return $verify > 0;
	}
	
	/**
	 * 验签
	 *
	 * @param string $data
	 * @param string $sign
	 * @param string $pem
	 * @return bool base64签名验签
	 */
	function verifyBase64($data, $sign, $public) {
		$p = openssl_pkey_get_public ( $public ) ;
		var_dump($p);
		$verify = openssl_verify ( $data, base64_decode( $sign ), $p );
		openssl_free_key ( $p );
		return $verify > 0;
	}
	
	/**
	 *
	 * @param unknown $publicKey
	 * @return string 更正私钥格式
	 */
	function privateKey($privateKey) {
		$pem = chunk_split($privateKey,64,"\n");
		$pem = "-----BEGIN RSA PRIVATE KEY-----\n".$pem."-----END RSA PRIVATE KEY-----\n";
		return $pem;
	}
	
	/**
	 *
	 * @param unknown $publicKey
	 * @return string 转换公钥格式
	 */
	function publicKey($publicKey) {
		$pem = chunk_split($publicKey,64,"\n");
		$pem = "-----BEGIN PUBLIC KEY-----\n".$pem."-----END PUBLIC KEY-----\n";
		return $pem;
	}
	

}
