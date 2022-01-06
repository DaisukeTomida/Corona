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
$CMN->log->info('editCompany.php start.');
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
if (isset($_POST["Type"])&&isset($_POST["CompanyCode"])){
	//ここに処理を書く（DB登録やファイルへの書き込みなど）
	$CMN->log->debug('editCompany start.');
	try{
		// 情報取得する
		$ip				= $_SERVER["REMOTE_ADDR"] ;	//IPアドレス
		$type			= intval($_POST["Type"]);
		$company_code	= intval($_POST["CompanyCode"]);
		$CMN->log->debug("type:[$type] company_code:[$company_code]");

		//ログ処理
		InsertLog("Info", $login_code, "editCompany", "ログインチェック IPアドレス[" . $ip . "]");
		//会社データチェック処理
		//新規の時のみ最大数を求める
		if($type==1){
			$CMN->log->debug("新規の時のみ最大数を求める");
			$sql  = "SELECT ";
			$sql .= "	 MAX(C.COMPANY_CODE) AS COMPANY_CODE ";
			$sql .= "FROM ";
			$sql .= "	" . DBPRE . "M_COMPANY AS C ";
			$sql .= "";
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->execute();
			$company_code = 1;
			if($stmt->rowCount()>0){
				$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
				$company_code = intval($row[0]) + 1;
			}
		}
		//会社データの存在チェック
		$CMN->log->debug("会社データの存在チェック");
		$sql  = "SELECT ";
		$sql .= "	 C.COMPANY_NAME ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "M_COMPANY AS C ";
		$sql .= "WHERE ";
		$sql .= "	C.COMPANY_CODE	= ? ";
		$sql .= "";
		$CMN->log->debug($sql);
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$CMN->log->debug("company_code:[$company_code]");
		$stmt->bindParam(1, $company_code, PDO::PARAM_INT);
		$stmt->execute();
		//データが存在しない場合
		if($stmt->rowCount() == 0){
			$data_exists=false;
		}else{
			$data_exists=true;
		}
		//ユーザーの存在チェック
		$CMN->log->debug("ユーザーの存在チェック");
		$sql  = "SELECT ";
		$sql .= "	 M.MEMBER_NAME ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "M_MEMBER AS M ";
		$sql .= "WHERE ";
		$sql .= "	M.COMPANY_CODE	= ? ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(1, $company_code, PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount() == 0){
			$member_exists=false;
		}else{
			$member_exists=true;
		}

		//データチェック処理
		$chkupd = false;
		if($type == 1 && $data_exists == true){
			//新規でデータが存在した場合
			$error="会社情報が存在しないため登録できません";
		}else if($type == 2 && $data_exists == false){
			//修正でデータが存在しない場合
			$error="会社情報が存在しないため登録できません";
		}else if($type == 9 && $member_exists == true){
			//削除でユーザーデータが存在する場合
			$error="所属ユーザーが存在するため削除できません";
		}
		$CMN->log->debug("error:[$error]");

		$sql  = "";
		if($$error==""){
			$company_name = $_POST["CompanyName"];	//COMPANY_NAME（会社名）
			$company_post = $_POST["CompanyPost"];	//COMPANY_POST（会社郵便番号）
			$company_ken = $_POST["CompanyKen"];		//COMPANY_KEN（会社都道府県）
			$company_shi = $_POST["CompanyShi"];		//COMPANY_SHI（会社市区町村）
			$company_add1 = $_POST["CompanyAdd1"];	//COMPANY_ADDRESS１（会社住所１）
			$company_add2 = $_POST["CompanyAdd2"];	//COMPANY_ADDRESS２（会社住所２）
			$company_tel = $_POST["CompanyTel"];		//COMPANY_TEL（会社電話番号）
			$company_web = $_POST["CompanyWeb"];		//COMPANY_WEB（会社ホームページ）
			$company_mail = $_POST["CompanyMail"];	//COMPANY_MAIL（会社メールアドレス）
			$company_host = $_POST["CompanyHost"];	//COMPANY_HOST（受信ホスト名）
			$company_port = intval($_POST["CompanyPort"]);			//COMPANY_PORT（受信ポート番号）
			$company_id = $_POST["CompanyId"];		//COMPANY_ID（受信ユーザー名）
			$company_pass = ConvSecret($_POST["CompanyPass"], 1);	//COMPANY_PASS（受信パスワード）
			if($type==1){
				$typename='新規';
				$sql  = "INSERT INTO " . DBPRE . "M_COMPANY( ";
				$sql .= "	 COMPANY_CODE ";
				$sql .= "	,COMPANY_NAME ";
				$sql .= "	,COMPANY_POST ";
				$sql .= "	,COMPANY_KEN ";
				$sql .= "	,COMPANY_SHI ";
				$sql .= "	,COMPANY_ADDRESS1 ";
				$sql .= "	,COMPANY_ADDRESS2 ";
				$sql .= "	,COMPANY_TEL ";
				$sql .= "	,COMPANY_WEB ";
				$sql .= "	,COMPANY_MAIL ";
				$sql .= "	,COMPANY_HOST ";
				$sql .= "	,COMPANY_PORT ";
				$sql .= "	,COMPANY_ID ";
				$sql .= "	,COMPANY_PASS ";
				$sql .= ")VALUES( ";
				$sql .= "	 :company_code ";
				$sql .= "	,:company_name ";
				$sql .= "	,:company_post ";
				$sql .= "	,:company_ken ";
				$sql .= "	,:company_shi ";
				$sql .= "	,:company_address1 ";
				$sql .= "	,:company_address2 ";
				$sql .= "	,:company_tel ";
				$sql .= "	,:company_web ";
				$sql .= "	,:company_mail ";
				$sql .= "	,:company_host ";
				$sql .= "	,:company_port ";
				$sql .= "	,:company_id ";
				$sql .= "	,:company_pass ";
				$sql .= ")";
				$sql .= "";
			}else if($type==2){
				$typename='修正';
				$sql  = "UPDATE " . DBPRE . "M_COMPANY ";
				$sql .= "SET ";
				$sql .= "	 COMPANY_NAME		= :company_name ";
				$sql .= "	,COMPANY_POST		= :company_post ";
				$sql .= "	,COMPANY_KEN		= :company_ken ";
				$sql .= "	,COMPANY_SHI		= :company_shi ";
				$sql .= "	,COMPANY_ADDRESS1	= :company_address1 ";
				$sql .= "	,COMPANY_ADDRESS2	= :company_address2 ";
				$sql .= "	,COMPANY_TEL		= :company_tel ";
				$sql .= "	,COMPANY_WEB		= :company_web ";
				$sql .= "	,COMPANY_MAIL		= :company_mail ";
				$sql .= "	,COMPANY_HOST		= :company_host ";
				$sql .= "	,COMPANY_PORT		= :company_port ";
				$sql .= "	,COMPANY_ID			= :company_id ";
				$sql .= "	,COMPANY_PASS		= :company_pass ";
				$sql .= "WHERE ";
				$sql .= "	COMPANY_CODE		= :company_code ";
				$sql .= "";
			}else if($type==9){
				$typename='削除';
				$sql  = "DELETE ";
				$sql .= "FROM ";
				$sql .= "	" . DBPRE . "M_COMPANY ";
				$sql .= "WHERE ";
				$sql .= "	COMPANY_CODE		= :company_code ";
				$sql .= "";
			}else{
				$typename='エラー';
				$error = "種類エラー";
			}
		}

		//チェックが正常に終了した場合は更新処理を行う
		if ($error==""){
			InsertLog('Info', $login_code, 'editCompany_ajax', '更新処理-開始');
			$CMN->log->info('update start.');
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam(':company_code', $company_code, PDO::PARAM_INT);
			if($type != 9){
				$stmt->bindParam(':company_name', $company_name, PDO::PARAM_STR);
				$stmt->bindParam(':company_post', $company_post, PDO::PARAM_STR);
				$stmt->bindParam(':company_ken', $company_ken, PDO::PARAM_STR);
				$stmt->bindParam(':company_shi', $company_shi, PDO::PARAM_STR);
				$stmt->bindParam(':company_address1', $company_add1, PDO::PARAM_STR);
				$stmt->bindParam(':company_address2', $company_add2, PDO::PARAM_STR);
				$stmt->bindParam(':company_tel', $company_tel, PDO::PARAM_STR);
				$stmt->bindParam(':company_web', $company_web, PDO::PARAM_STR);
				$stmt->bindParam(':company_mail', $company_mail, PDO::PARAM_STR);
				$stmt->bindParam(':company_host', $company_host, PDO::PARAM_STR);
				$stmt->bindParam(':company_port', $company_port, PDO::PARAM_INT);
				$stmt->bindParam(':company_id', $company_id, PDO::PARAM_STR);
				$stmt->bindParam(':company_pass', $company_pass, PDO::PARAM_STR);
			}
			InsertLog('Info', $login_code, 'editCompany_ajax', 'SQL実行：'.$typename.'登録　会社コード['.$company_code.']');
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
			InsertLog('Info', $login_code, 'editCompany_ajax', '更新処理-終了');
		}
		if ($error != ""){
			$CMN->error = $error;
			$CMN->log->warn("システムエラー:" . $CMN->error);
			InsertLog('Warn', $login_code, 'editCompany_ajax', $CMN->error);
			echo $CMN->error;
		}
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
		$CMN->log->error("システムエラー:" . $CMN->error);
		InsertLog('Error', $login_code, 'editCompany_ajax', $CMN->error);
		echo $CMN->error;
	}
	$CMN->log->debug('Check End.');
}else{
	$CMN->error = 'The parameter of "request" is not found.';
	$CMN->log->error("パラメータエラー処理:" . $CMN->error);
	echo $CMN->error;
}
?>