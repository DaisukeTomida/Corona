<?php
/******
 * 初期値宣言
 *********/
session_start();
require_once("./line_connect.php");
require_once("./common/const.php");
require_once("./common/db.php");
require_once("./common/logger.php");
require_once("./common/display.php");
require_once("./common/function.php");

/******
 * 共通処理
 *********/
$CMN 			= new stdClass;
$MY				= new stdClass;
if($_SESSION["MY"] != null){
	$MY = $_SESSION["MY"];
}
$CMN->log = Logger::getInstance();
// $CMN->log->error("error log.");
// $CMN->log->warn("warn log.");
// $CMN->log->info("info log.");
// $CMN->log->debug("debug log.");
$display_code		= $_GET['d'];
//DB接続処理
$CMN->log->info("DBOpen start.");
if (DBOpen() == false){
	$CMN->log->error("DBOpen error.  $CMN->error");
	$display_code = "404";
}
$CMN->log->info("DBOpen success.");
if($display_code!="conv"){
	if($display_code=="" || $_SESSION["MY"] == null){
		$display_code = "login";
	}
}
/******
 * 画面表示処理
 *********/
//画面の遷移先を取得する
$CMN->log->info("[$display_code]:GetDisplay start.");
if (GetDisplay($display_code) == false){
	$CMN->log->error("[$display_code]:GetDisplay error.  $CMN->error");
	$display_code = "404";
	GetDisplay($display_code);
}
$CMN->log->info("[$display_code]:GetDisplay end.");
if($display_code == "login" || $display_code == "404"){
	session_destroy();
}
//画面機能のファイル存在チェック
$disp_flg = file_exists($MY->display["display_file"]);
//対象のファイルが存在した場合、画面の情報を呼び出す
if($disp_flg){
	require_once($MY->display["display_file"]);
}
Header_Display($MY->display["display_name"], SYSTEM_NAME);
Body_Display($display_code);
$CMN->log->info($MY->display["display_name"] . " fileopen start.");
try{
	//3.個別jsプラグインの読み込み-->
	if($disp_flg){
		//スクリプト表示処理
		DispScript();
	}
	DispBody();
}catch(Exception $ex){
	$CMN->error =  $ex->getMessage();
	$CMN->log->error($CMN->error);
}
$CMN->log->info($MY->display["display_name"] . " fileopen end.");
if(DEBUG_STATUS == "true"){
	if($CMN->error!=""){
		echo "<h3>ERROR</h3>";
		echo $CMN->error;
	}
	echo "<h3>MY</h3>";
	echo "<pre>"; print_r($MY); echo "</pre>";
	echo "<h3>CMN</h3>";
	echo "<pre>"; print_r($CMN); echo "</pre>";
	echo "<h3>SERVER</h3>";
	echo "<pre>"; print_r($_SERVER); echo "</pre>";
}
Footer_Display();
?>