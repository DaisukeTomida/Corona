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
$CMN->log->info('editContact.php start.');
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
if (isset($_POST["type"])){
	/*************************
		【利用者情報更新処理】
		パラメータ
			Type	: 1:新規 2:修正 3:有効・無効切替　8:更新（自ユーザー） 9:削除
	**************************/
	//ここに処理を書く（DB登録やファイルへの書き込みなど）
	$CMN->log->debug('editContact start. type:' . $_POST["type"]);
	try{
		// 情報取得する
		$type				= $_POST["type"];
		$user_id			= $_POST["user_id"];
		$user_status		= $_POST["user_status"];
		if($type == "updateStatus"){
			$user_status_bef	= $_POST["user_status_bef"];
			$route				= "STATUS";
			$message			= "状態を" . $STATUS_ARRAY[$user_status_bef] . "から" . $STATUS_ARRAY[$user_status] . "に変更(" . $MY->member["name"] . ")";
		}elseif($type == "updateMessage"){
			$route				= "MESSAGE";
			$message			= $_POST["message"] . "(" . $MY->member["name"] . ")";
		}
		$CMN->log->debug("route:". $route);
		$CMN->log->debug("message:". $message);
		//トランザクション処理を開始
		$CMN->dbh->beginTransaction();
		try {
			$sql  = "INSERT INTO " . DBPRE . "ACCESS( ";
			$sql .= "	  USER_ID ";
			$sql .= "	 ,ACCESS_DATE ";
			$sql .= "	 ,ROUTE ";
			$sql .= "	 ,MESSAGE ";
			$sql .= ")VALUES( ";
			$sql .= "	  :user_id ";
			$sql .= "	 ,CURRENT_TIMESTAMP() ";
			$sql .= "	 ,:route ";
			$sql .= "	 ,:message ";
			$sql .= ")";
			$CMN->log->info('update start.');
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
			$stmt->bindParam(':route', $route, PDO::PARAM_STR);
			$stmt->bindParam(':message', $message, PDO::PARAM_STR);
			$stmt->execute();

			$sql  = "UPDATE " . DBPRE . "USER ";
			$sql .= "SET ";
			$sql .= "	 USER_STATUS		= :user_status ";
			if($type == "updateMessage"){
				$sql .= "	 ,LAST_ACCESS		= CURRENT_TIMESTAMP() ";
			}
			$sql .= "WHERE ";
			$sql .= "	 USER_ID			= :user_id ";
			$sql .= "";
			$CMN->log->info('update start.');
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
			$stmt->bindParam(':user_status', $user_status, PDO::PARAM_STR);
			$stmt->execute();

			//コミット
			$CMN->dbh->commit();
			$CMN->log->debug('update success.');
		}catch(Exception $ex){
			//ロールバック
			$CMN->log->debug('update rollback.');
			$CMN->dbh->rollback();
			throw $ex;
		}
		InsertLog('Info', $login_code, 'editContact_ajax', '更新処理-終了');
		if ($error != ""){
			$CMN->error = $error;
			$CMN->log->warn("システムエラー:" . $CMN->error);
			InsertLog('Warn', $login_code, 'editContact_ajax', $CMN->error);
			echo $CMN->error;
		}
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
		$CMN->log->error("システムエラー:" . $CMN->error);
		InsertLog('Error', $login_code, 'editContact_ajax', $CMN->error);
		echo $CMN->error;
	}
	$CMN->log->debug('Check End.');
}else{
	$CMN->error = 'The parameter of "request" is not found.';
	$CMN->log->error("パラメータエラー処理:" . $CMN->error);
	echo $CMN->error;
}
?>