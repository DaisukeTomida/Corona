<?php
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI';
if($host=="test.jp"){
	//テスト用データ情報
	define("DATABASE_URL", '');
	define("DATABASE_USRID", '');
	define("DATABASE_PASS", '');
	define("SYSTEM_NAME", '[テスト]自宅療養連絡ツール');
	define("VERSION", '1.0.0');
	define("DEBUG_STATUS", 'true');
}else{
	//本番用用データ情報
	define("DATABASE_URL", '');
	define("DATABASE_USRID", '');
	define("DATABASE_PASS", '');
	define("SYSTEM_NAME", '自宅療養連絡ツール');
	define("VERSION", '1.0.0');
	define("DEBUG_STATUS", 'false');
}
//データベーステーブルのプレフィックス
define("DBPRE", "CORONA_");

define("SSL_KEY", '暗号化用のキー');
define("IV", '9999999999999999');
?>
