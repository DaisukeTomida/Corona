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
$CMN->log->info('editMember.php start.');
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
if (isset($_POST["Type"])&&isset($_POST["MemberCode"])){
	/*************************
		【ユーザー情報更新処理】
		パラメータ
			Type	: 1:新規 2:修正 3:有効・無効切替　8:更新（自ユーザー） 9:削除
	**************************/
	//ここに処理を書く（DB登録やファイルへの書き込みなど）
	$CMN->log->debug('editMember start.');
	try{
		// 情報取得する
		$ip				= $_SERVER["REMOTE_ADDR"] ;	//IPアドレス
		$type			= intval($_POST["Type"]);
		$member_code		= intval($_POST["MemberCode"]);

		//ログ処理
		InsertLog("Info", $login_code, "editMember", "ログインチェック IPアドレス[" . $ip . "]");
		//ユーザーデータび存在チェック
		$CMN->log->debug("ユーザーデータの存在チェック");
		$sql  = "SELECT ";
		$sql .= "	 M.MEMBER_ENABLE ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "M_MEMBER AS M ";
		$sql .= "WHERE ";
		$sql .= "	M.MEMBER_CODE	= ? ";
		$sql .= "";
		$CMN->log->debug($sql);
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$CMN->log->debug("member_code:[$member_code]");
		$stmt->bindParam(1, $member_code, PDO::PARAM_INT);
		$stmt->execute();
		//データが存在しない場合
		$member_enable = 1;
		if($stmt->rowCount() == 0){
			$data_exists=false;
		}else{
			$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
			$member_enable = intval($row[0]);
			$data_exists=true;
		}

		if($member_code==$login_code && $type!=8){
			//自ユーザーログイン中で自ユーザー更新処理じゃない場合
			$error="ログイン中のため処理できません";
		}else if($type == 1 && $data_exists == true){
			//新規でデータが存在した場合
			$error="作業者情報が存在しないため登録できません";
		}else if($type == 2 && $data_exists == false){
			//修正でデータが存在しない場合
			$error="作業者情報が存在しないため登録できません";
		}else if($type == 3 && $data_exists == false){
			//切替でデータが存在しない場合
			$error="作業者情報が存在しないため切替できません";
		}else if($type == 8 && $data_exists == false){
			//更新でデータが存在しない場合
			$error="作業者情報が存在しないため更新できません";
		}
		$CMN->log->debug("error:[$error]");

		$sql  = "";
		if($$error==""){
			$company_code = intval($_POST["CompanyCode"]);		//COMPANY_CODE（会社コード）
			$member_pass = ConvSecret($_POST["MemberPass"], 1);		//MEMBER_PASS（パスワード）
			$member_name = $_POST["MemberName"];		//MEMBER_NAME（ユーザー名）
			$member_mail = $_POST["MemberMail"];		//MEMBER_MAIL（ユーザーメールアドレス）
			$member_auth = intval($_POST["MemberAuth"]);			//MEMBER_AUTHORITY（ユーザー権限）
			if($type==1){
				$typename='新規';
				$sql  = "INSERT INTO " . DBPRE . "M_MEMBER( ";
				$sql .= "	 COMPANY_CODE ";
				$sql .= "	,MEMBER_CODE ";
				$sql .= "	,MEMBER_PASS ";
				$sql .= "	,MEMBER_NAME ";
				$sql .= "	,MEMBER_MAIL ";
				$sql .= "	,MEMBER_AUTHORITY ";
				$sql .= "	,MEMBER_ENABLE ";
				$sql .= ")VALUES( ";
				$sql .= "	 :company_code ";
				$sql .= "	,:member_code ";
				$sql .= "	,:member_pass ";
				$sql .= "	,:member_name ";
				$sql .= "	,:member_mail ";
				$sql .= "	,:member_auth ";
				$sql .= "	,:member_enable ";
				$sql .= ")";
				$sql .= "";
			}else if($type==2||$type==8){
				$typename = $type==2 ? '修正' : '更新';
				//修正登録
				$sql  = "UPDATE " . DBPRE . "M_MEMBER ";
				$sql .= "SET ";
				$sql .= "	 COMPANY_CODE		= :company_code ";
				$sql .= "	,MEMBER_PASS		= :member_pass ";
				$sql .= "	,MEMBER_NAME		= :member_name ";
				$sql .= "	,MEMBER_MAIL		= :member_mail ";
				$sql .= "	,MEMBER_AUTHORITY	= :member_auth ";
				$sql .= "	,MEMBER_ENABLE		= :member_enable ";
				$sql .= "WHERE ";
				$sql .= "	 MEMBER_CODE		= :member_code ";
				$sql .= "";
			}else if($type==3){
				$typename='切替';
				$member_enable = $member_enable == 0 ? 1 : 0;
				$sql  = "UPDATE " . DBPRE . "M_MEMBER ";
				$sql .= "SET ";
				$sql .= "	MEMBER_ENABLE	= :member_enable ";
				$sql .= "WHERE ";
				$sql .= "	MEMBER_CODE		= :member_code ";
				$sql .= "";
			}else if($type==9){
				$typename='削除';
				$sql  = "DELETE ";
				$sql .= "FROM ";
				$sql .= "	" . DBPRE . "M_MEMBER ";
				$sql .= "WHERE ";
				$sql .= "	MEMBER_CODE		= :member_code ";
				$sql .= "";
			}else{
				$typename='エラー';
				$error = "種類エラー";
			}
		}

		//チェックが正常に終了した場合は更新処理を行う
		if ($error==""){
			InsertLog('Info', $login_code, 'editMember_ajax', '更新処理-開始');
			$CMN->log->info('update start.');
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam(':member_code', $member_code, PDO::PARAM_STR);
			if($type == 1 || $type == 2 || $type == 8){
				$stmt->bindParam(':company_code', $company_code, PDO::PARAM_INT);
				$stmt->bindParam(':member_pass', $member_pass, PDO::PARAM_STR);
				$stmt->bindParam(':member_name', $member_name, PDO::PARAM_STR);
				$stmt->bindParam(':member_mail', $member_mail, PDO::PARAM_STR);
				$stmt->bindParam(':member_auth', $member_auth, PDO::PARAM_STR);
			}
			if($type != 9){
				$stmt->bindParam(':member_enable', $member_enable, PDO::PARAM_INT);
			}
		}
		InsertLog('Info', $login_code, 'editMember_ajax', 'SQL実行：'.$typename.'登録　作業者コード['.$member_code.']');
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
		InsertLog('Info', $login_code, 'editMember_ajax', '更新処理-終了');
		if ($error != ""){
			$CMN->error = $error;
			$CMN->log->warn("システムエラー:" . $CMN->error);
			InsertLog('Warn', $login_code, 'editMember_ajax', $CMN->error);
			echo $CMN->error;
		}
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
		$CMN->log->error("システムエラー:" . $CMN->error);
		InsertLog('Error', $login_code, 'editMember_ajax', $CMN->error);
		echo $CMN->error;
	}
	$CMN->log->debug('Check End.');
}else{
	$CMN->error = 'The parameter of "request" is not found.';
	$CMN->log->error("パラメータエラー処理:" . $CMN->error);
	echo $CMN->error;
}
?>