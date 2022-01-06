<?php
/****************************************
	データベース接続処理の設定
*****************************************/
function DBOpen(){

	global $CMN;

	$rtn = false;
	try{
		$CMN->dbh = new PDO(DATABASE_URL, DATABASE_USRID, DATABASE_PASS);
		// 静的プレースホルダを指定
		$CMN->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		// エラー発生時に例外を投げる
		$CMN->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$rtn = true;
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
	}
	return $rtn;
}
/****************************************
	ログ書き込み処理
		パラメータ
			$type			: ログタイプ(10)
			$member_code	: ユーザーコード
			$filename		: 画面物理名
			$message		: メッセージ
		戻り値
			なし
*****************************************/
function InsertLog($type, $member_code, $filename, $message){

	// $script_name = $_SERVER['SCRIPT_NAME'];

	global $CMN;

	//パラメータ
	//ログの更新
	$stmt = $CMN->dbh->prepare("INSERT INTO " . DBPRE . "W_LOG (LOG_TYPE, MEMBER_CODE, LOG_FILENAME, LOG_MESSAGE)VALUES(?, ?, ?, ?)");
	//トランザクション処理を開始
	$CMN->dbh->beginTransaction();
	try {
		$stmt->bindParam(1, $type, PDO::PARAM_STR);
		$stmt->bindParam(2, $member_code, PDO::PARAM_STR);
		$stmt->bindParam(3, $filename, PDO::PARAM_STR);
		$stmt->bindParam(4, $message, PDO::PARAM_STR);
		$CMN->log->debug("InsertLog.[" . DBPRE . "W_LOG ] type:[$type] member_code:[$member_code] filename:[$filename] message:[$message]");
		$stmt->execute();
		//コミット
		$CMN->dbh->commit();
	}catch(Exception $ex){
		//ロールバック
		$CMN->log->error('Log error. ' . $ex->getMessage());
		$CMN->dbh->rollback();
		throw $ex;
	}



}
function GetDBMember($MemberCode){

	global $CMN;
	global $MY;

	$CMN->log->info('GetDBMember start.');
	try{
		//////////////////////
		//	初期値登録
		//////////////////////
		$MY->member		= null;
		//ユーザーマスタから情報を取得
		$sql  = "SELECT ";
		$sql .= "	  M.COMPANY_CODE ";
		$sql .= "	 ,C.COMPANY_NAME ";
		$sql .= "	 ,M.MEMBER_CODE ";
		$sql .= "	 ,M.MEMBER_PASS ";
		$sql .= "	 ,M.MEMBER_NAME ";
		$sql .= "	 ,M.MEMBER_MAIL ";
		$sql .= "	 ,M.MEMBER_AUTHORITY ";
		$sql .= "	 ,M.MEMBER_ENABLE ";
		$sql .= "	 ,M.MEMBER_LOGIN ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "M_MEMBER AS M ";
		$sql .= "		LEFT JOIN " . DBPRE . "M_COMPANY AS C ";
		$sql .= "			ON	C.COMPANY_CODE	= M.COMPANY_CODE ";
		$sql .= "WHERE ";
		$sql .= "		M.MEMBER_CODE	= :member_code ";
		$sql .= "AND	M.MEMBER_ENABLE	= 1 ";

		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(':member_code', $MemberCode, PDO::PARAM_STR);
		$stmt->execute();

		while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {			
			$cnt = $row[1];
			$MY->member["company"]		= $row[0];					//COMPANY_CODE（会社コード）
			$MY->member["company_name"]	= $row[1];					//COMPANY_CODE（会社名）
			$MY->member["code"]			= $row[2];					//MEMBER_CODE（ユーザーコード）
			$MY->member["pass"]			= ConvSecret($row[3], 9);	//MEMBER_PASS（パスワード）
			$MY->member["name"]			= $row[4];					//MEMBER_NAME（ユーザー名）
			$MY->member["mail"]			= $row[5];					//MEMBER_MAIL（ユーザーメールアドレス）
			$MY->member["authority"]	= intval($row[6]);			//MEMBER_AUTHORITY（ユーザー権限）
			$MY->member["enable"]		= intval($row[7]);			//MEMBER_ENABLE（ユーザー有効フラグ）
			$MY->member["login"]		= $row[8];					//MEMBER_LOGIN（ログイン識別番号）
		}
		$CMN->log->info('GetDBMember end.');
	}catch(Exception $ex){
		$CMN->log->error('GetDBMember error. ' . $ex->getMessage());
		throw $ex;
	}

}
/*************************************

	ここから更新処理

**************************************/
function UpdateDBMember_login(){

	global $MY;
	global $CMN;

	$CMN->log->debug("UpdateDBMember_login start.");
	try{
		//ユーザーマスタを更新
		$sql  = "UPDATE " . DBPRE . "M_MEMBER ";
		$sql .= "SET ";
		$sql .= "	  MEMBER_LOGIN 		= :member_login ";
		$sql .= "WHERE ";
		$sql .= "	MEMBER_CODE		= :member_code ";
		$sql .= "";
		$CMN->log->debug($sql);
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(':member_code', $MY->member["code"], PDO::PARAM_STR);						//MEMBER_CODE（ユーザーコード）
		$stmt->bindParam(':member_login', $MY->member["login"], PDO::PARAM_STR);		//MEMBER_LOGIN（ログイン識別番号）
		//ログ処理
		$CMN->log->debug("log start.");
		//トランザクション処理を開始
		$CMN->log->debug("beginTransaction start.");
		$CMN->dbh->beginTransaction();
		try {
			$CMN->log->debug("execute start.");
			$stmt->execute();
			//コミット
			$CMN->log->debug("commit start.");
			$CMN->dbh->commit();
		}catch(Exception $ex){
			//ロールバック
			$CMN->log->debug('UpdateDBMember rollback.');
			$CMN->dbh->rollback();
			throw $ex;
		}
		$CMN->log->debug("UpdateDBMember_login end.");
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
		$CMN->log->error("UpdateDBMember_login error. [$CMN->error]");
		throw $ex;
	}
	$CMN->log->debug("UpdateDBMember_login end.");

}


/****************************************
	コード名称取得処理
		パラメータ
			$kubun			: 区分
			$m_code			: コード
		戻り値
			エラーメッセージ
*****************************************/
function getCode($kubun, $code){

	global $MY;
	global $CMN;

	$result = false;

	//////////////////////
	//	初期値登録
	//////////////////////
	$CMN->code->error_message='';
	try{

		if($kubun==""){
			$CMN->code->error_message = '区分が入力されていません';
		}else{
			//ログ処理
//			InsertLog('Info', $MY->login_code, 'getCode', 'コード名称取得処理-開始　パラメータ　区分['.$kubun.']コード['.$code.']');
			$sql  = "SELECT ";
			$sql .= "	 C.CODE ";
			$sql .= "	,C.NAME ";
			$sql .= "FROM ";
			$sql .= "	ODR_M_CODE AS C ";
			$sql .= "WHERE ";
			$sql .= "	C.KUBUN	= :kubun ";
			if($code!=""){
				$sql .= "AND ";
				$sql .= "	C.CODE	= :code ";
			}
			$sql .= "ORDER BY ";
			$sql .= "	C.CODE ";

			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam(':kubun', $kubun, PDO::PARAM_STR);

			if($code!=""){
				$stmt->bindParam(':code', $code, PDO::PARAM_STR);
			}

			$stmt->execute();

			while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				$code		= $row[0];
				//CODE（コード）
				$CMN->code->row[$code]->db_code		= intval($row[0]);
				//NAME（名称）
				$CMN->code->row[$code]->db_name		= $row[1];
			}

		}

		$result = true;

	}catch(Exception $e){
		$CMN->code->error_message = $e->getMessage();
	}
	return $result;

}
?>