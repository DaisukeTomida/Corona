<?php
/******
 * 初期値宣言
 *********/
session_start();
require_once("../line_connect.php");
require_once("../common/const.php");
require_once("../common/db.php");
require_once("../common/logger.php");
require_once("../common/function.php");
/******
 * 共通処理
 *********/
$CMN 			= new stdClass;
$MY				= new stdClass;
if($_SESSION["MY"] != null){
	$MY = $_SESSION["MY"];
}
$login_code = $MY->member["code"] != "" ? $MY->member["code"] : "sys" ;
$CMN->log = Logger::getInstance();
$CMN->log->info('chklogin.php start.');
$CMN->log->info("code:" . $MY->member["code"]);
// $CMN->log->error("error log.");
// $CMN->log->warn("warn log.");
// $CMN->log->info("info log.");
// $CMN->log->debug("debug log.");
//DB接続処理
$CMN->log->info("DBOpen start.");
if (DBOpen() == false){
	echo $CMN->error;
	$CMN->log->error("DBOpen error.  $CMN->error");
}
$CMN->log->info("DBOpen success.");
/******
 * 非同期共通処理
 *********/
header("Content-type: text/plain; charset=UTF-8");
if (isset($_POST["MemberCode"])&&isset($_POST["PassWord"])){
	//ここに処理を書く（DB登録やファイルへの書き込みなど）
	$CMN->log->debug('chklogin start.');
	try{
		// 情報取得する
		$ip				= $_SERVER["REMOTE_ADDR"] ;	//IPアドレス
		$member_code	= $_POST["MemberCode"];		//ユーザーID
		$member_pass	= $_POST["PassWord"];		//パスワード

		//ログ処理
		InsertLog("Info", $login_code, "chklogin_ajax", "ログインチェック IPアドレス[" . $ip . "]");
		//会員情報取得
		GetDBMember($member_code);

		$CMN->log->debug('Check start.');
		if($MY->member["name"]==""){
			$CMN->error = "対象ユーザーは存在しません";
		}elseif($member_pass != $MY->member["pass"]){
			$CMN->error = "対象ユーザーは存在しません";
		}elseif($MY->member["enable"]==0){
			$CMN->error = "対象ユーザーは現在利用できません";
		}else{
			$MY->member["login"]			= mobileId();
			$CMN->log->info('OK:'.$MY->member["login"]);
			UpdateDBMember_login();
			$_SESSION['MY']	= $MY;
		}
		if($CMN->error != ""){
			$CMN->log->warn("システムエラー:" . $CMN->error);
			InsertLog('Warn', $login_code, 'chklogin_ajax', $CMN->error);
			echo $CMN->error;
		}
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
		$CMN->log->error("システムエラー:" . $CMN->error);
		InsertLog('Error', $login_code, 'chklogin_ajax', $CMN->error);
		echo $CMN->error;
	}
	$CMN->log->debug('Check End.');
}else{
	$CMN->error = 'The parameter of "request" is not found.';
	$CMN->log->error("パラメータエラー処理:" . $CMN->error);
	echo $CMN->error;
}
?>