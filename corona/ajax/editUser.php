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
$CMN->log->info('editUser.php start.');
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
	$CMN->log->debug('editUser start.');
	try{
		// 情報取得する
		$ip					= $_SERVER["REMOTE_ADDR"] ;	//IPアドレス
		$type				= intval($_POST["type"]);
		if($type == 1){
			$user_id			= uniqid();
			$CMN->log->debug('新規USER ID :' . $user_id);
		}else{
			$user_id			= $_POST["user_id"];
		}
		$user_name			= $_POST["user_name"];
		$user_contact_no	= $_POST["user_contact_no"];
		$user_address		= $_POST["user_address"];
		$user_age			= intval($_POST["user_age"]);
		$user_sex			= $_POST["user_sex"];
		$user_status		= $_POST["user_status"];
		$company_code		= intval($MY->member["company"]);
		//ログ処理
		InsertLog("Info", $login_code, "editUser", "ログインチェック IPアドレス[" . $ip . "]");
		//利用者ーデータび存在チェック
		$CMN->log->debug("利用者データの存在チェック");
		$sql  = "SELECT ";
		$sql .= "	 U.USER_ID ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "USER AS U ";
		$sql .= "WHERE ";
		$sql .= "	U.USER_ID	= :user_id ";
		$sql .= "";
		$CMN->log->debug($sql);
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$CMN->log->debug("user_id:[$user_id]");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		//データが存在しない場合
		if($stmt->rowCount() == 0){
			$data_exists=false;
		}else{
			$data_exists=true;
		}
		if($type == 1 && $data_exists == true){
			//新規でデータが存在した場合
			$error="利用者情報が存在しないため登録できません";
		}else if($type == 2 && $data_exists == false){
			//修正でデータが存在しない場合
			$error="利用者情報が存在しないため登録できません";
		}else if($type == 8 && $data_exists == false){
			//状態変更でデータが存在しない場合
			$error="利用者情報が存在しないため変更できません";
		}
		$CMN->log->debug("error:[$error]");
		if($error==""){
			if($type==1){
				$typename='新規';
				$sql  = "INSERT INTO " . DBPRE . "USER( ";
				$sql .= "	  USER_ID ";
				$sql .= "	 ,COMPANY_CODE ";
				$sql .= "	 ,USER_NAME ";
				$sql .= "	 ,USER_CONTACT_NO ";
				$sql .= "	 ,USER_ADDRESS ";
				$sql .= "	 ,USER_AGE ";
				$sql .= "	 ,USER_SEX ";
				$sql .= "	 ,USER_STATUS ";
				$sql .= "	 ,U.LAST_ACCESS ";
				$sql .= ")VALUES( ";
				$sql .= "	  :user_id ";
				$sql .= "	 ,:company_code ";
				$sql .= "	 ,:user_name ";
				$sql .= "	 ,:user_contact_no ";
				$sql .= "	 ,:user_address ";
				$sql .= "	 ,:user_age ";
				$sql .= "	 ,:user_sex ";
				$sql .= "	 ,'ENTRY' ";
				$sql .= "	 ,CURRENT_TIMESTAMP() ";
				$sql .= ")";
				$sql .= "";
			}elseif($type==2){
				$typename = '修正';
				//修正登録
				$sql  = "UPDATE " . DBPRE . "USER ";
				$sql .= "SET ";
				$sql .= "	 USER_NAME			= :user_name ";
				$sql .= "	,USER_CONTACT_NO	= :user_contact_no ";
				$sql .= "	,USER_ADDRESS		= :user_address ";
				$sql .= "	,USER_AGE			= :user_age ";
				$sql .= "	,USER_SEX			= :user_sex ";
				$sql .= "WHERE ";
				$sql .= "	 USER_ID			= :user_id ";
				$sql .= "";
			}elseif($type==8){
				$typename = '状態変更';
				$sql  = "UPDATE " . DBPRE . "USER ";
				$sql .= "SET ";
				$sql .= "	 USER_STATUS		= :user_status ";
				$sql .= "WHERE ";
				$sql .= "	 USER_ID			= :user_id ";
				$sql .= "";
			}else{
				$typename='エラー';
				$error = "種類エラー";
			}
		}
		//チェックが正常に終了した場合は更新処理を行う
		if ($error==""){
			InsertLog('Info', $login_code, 'editUser_ajax', '更新処理-開始');
			$CMN->log->debug($sql);
			$CMN->log->info('update start.');
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
			if($type == 1){
				$stmt->bindParam(":company_code", $company_code, PDO::PARAM_INT);
			}
			if($type == 1 || $type == 2){
				$stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
				$stmt->bindParam(':user_contact_no', $user_contact_no, PDO::PARAM_STR);
				$stmt->bindParam(':user_address', $user_address, PDO::PARAM_STR);
				$stmt->bindParam(':user_age', $user_age, PDO::PARAM_INT);
				$stmt->bindParam(':user_sex', $user_sex, PDO::PARAM_STR);
			}
			if($type == 8){
				$stmt->bindParam(':user_status', $user_status, PDO::PARAM_STR);
			}
		}
		InsertLog('Info', $login_code, 'editUser_ajax', 'SQL実行：'.$typename.'登録　作業者コード['.$member_code.']');
		$CMN->log->debug('sql ' . $sql);
		//トランザクション処理を開始
		$CMN->dbh->beginTransaction();
		try {
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
		InsertLog('Info', $login_code, 'editUser_ajax', '更新処理-終了');
		if ($error != ""){
			$CMN->error = $error;
			$CMN->log->warn("システムエラー:" . $CMN->error);
			InsertLog('Warn', $login_code, 'editUser_ajax', $CMN->error);
			echo $CMN->error;
		}
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
		$CMN->log->error("システムエラー:" . $CMN->error);
		InsertLog('Error', $login_code, 'editUser_ajax', $CMN->error);
		echo $CMN->error;
	}
	$CMN->log->debug('Check End.');
}else{
	$CMN->error = 'The parameter of "request" is not found.';
	$CMN->log->error("パラメータエラー処理:" . $CMN->error);
	echo $CMN->error;
}
?>