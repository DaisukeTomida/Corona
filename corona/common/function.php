<?php

/****************************************
	指定した二つの日付の差を調べる
		パラメータ
			$date1	: 日付１
			$date2	: 日付２
		戻り値
			日数
*****************************************/
function day_diff($date1, $date2) {
 
	$datetime1 = new DateTime($date1);
	$datetime2 = new DateTime($date2);
 
	$interval = $datetime1->diff($datetime2);
	$diff_date = "";
	if($interval->format('%y')>0){
		$diff_date .= $interval->format('%y年');
	}
	if($interval->format('%m')>0){
		$diff_date .= $interval->format('%m月');
	}
	if($interval->format('%d')>0){
		$diff_date .= $interval->format('%d日');
	}
	if($interval->format('%h')>0){
		$diff_date .= $interval->format('%h時');
	}
	if($interval->format('%i')>0){
		$diff_date .= $interval->format('%i分');
	}
	$diff_date .= $interval->format('%s秒経過');
 	// 戻り値
	return $diff_date;
 
}
/****************************************
	//個体識別番号取得処理
		パラメータ
			なし
		戻り値
			個体識別番号
*****************************************/
function mobileId() {
	if (isset($_SERVER['HTTP_X_DCMGUID'])) {
		//ドコモ
		$mobile_id = $_SERVER['HTTP_X_DCMGUID'];
	}else if (isset($_SERVER['HTTP_X_UP_SUBNO'])) {
		//Au
		$mobile_id = $_SERVER['HTTP_X_UP_SUBNO'];
	}else if (isset($_SERVER['HTTP_X_JPHONE_UID'])) {
		//ソフトバンク
		$mobile_id = $_SERVER['HTTP_X_JPHONE_UID'];
	}else{
		//PC
		$mobile_id = $_SERVER['HTTP_USER_AGENT'];
	}
 	return $mobile_id;
}
/****************************************
	メール送信機能
		パラメータ
			$dbh				: データベース接続
			$company_code		: 会社コード
			$to				: 送信先メールアドレス
			$subject			: 件名（日本語OK）
			$body			: 本文（日本語OK）
		戻り値
			エラーメッセージ
*****************************************/
function SendMail($dbh, $company_code, $to, $subject, $body){

	//Qdmailをロード
	require_once('qdmail.php');
	//Qdsmtpをロード（ドキュメントには、記述不要とかいてあるが、書かないとうまくいかないことがあった）
	require_once('qdsmtp.php');

	$message="";
	//会社情報取得
	$sql  = "SELECT ";
	$sql .= "	 C.COMPANY_NAME ";
	$sql .= "	,C.COMPANY_MAIL ";
	$sql .= "	,C.COMPANY_HOST ";
	$sql .= "	,C.COMPANY_PORT ";
	$sql .= "	,C.COMPANY_ID ";
	$sql .= "	,C.COMPANY_PASS ";
	$sql .= "FROM ";
	$sql .= "	" . DBPRE . "M_COMPANY AS C ";
	$sql .= "WHERE ";
	$sql .= "	C.COMPANY_CODE = :company_code ";
	$sql .= "";
	$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->bindParam(':company_code', $company_code, PDO::PARAM_INT);
	$stmt->execute();
	while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		//COMPANY_NAME（会社名）
		$company_name = $row[0];
		//COMPANY_MAIL（会社メールアドレス）
		$company_mail = $row[1];
		//COMPANY_HOST（受信ホスト名）
		$company_host = $row[2];
		//COMPANY_PORT（受信ポート番号）
		$company_port = intval($row[3]);
		//COMPANY_ID（受信ユーザー名）
		$company_id = $row[4];
		//COMPANY_PASS（受信パスワード）
		$company_pass = ConvSecret($row[5], 9);
	}
	if($company_mail=='' || $company_host=='' || 
		$company_port==0 || $company_id=='' || 
		$company_pass==''){
		$message="メール送信の設定がされていません";
	}else{
		//SMTP送信
		$mail = new Qdmail();
		$mail -> smtp(true);
		$param = array(
			 'host'	=> $company_host
			,'port'	=> $company_port
			,'from'	=> $company_mail
			,'protocol'	=> 'SMTP_AUTH'
			,'user'	=> $company_id
			,'pass'	=> $company_pass
		);
		$mail ->smtpServer($param);
		// 送信元設定
		$mail ->to($to);
		$mail ->subject($subject);
		$mail ->from($company_mail, $company_name);
		$mail ->text($body);
		// メール送信
		if (!$mail ->send()){
			$message="メール送信に失敗しました";
		}
	}
	return $message;

}
function GetUrl(){
	return (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . '/top';
}
/****************************************
	暗号化・複合化処理
		パラメータ
			$value		: 対象の値
			$type		: 1:暗号化 9:複合化
		戻り値
			処理済みの値
			エラー時は未変換
*****************************************/
function ConvSecret($value, $type){
	$conv_value		= $value;
	if($type==1){
		$conv_value		= openssl_encrypt($value, 'AES-128-ECB', SSL_KEY, 0, IV);		//暗号化
	}elseif($type==9){
		$conv_value		= openssl_decrypt($value, 'AES-128-ECB', SSL_KEY, 0, IV);		//複合化
	}
	return $conv_value;
}
/****************************************
	改行処理
		パラメータ
			$value		: 対象の値
			$type		: 1:<br>変換 9:\r\n変換
		戻り値
			処理済みの値
			エラー時は未変換
*****************************************/
function ConvLineBreak($value, $type){
	$conv_value		= $value;
	if($type==1){
		 //改行コードを統一にする
		$cr = array("\r\n", "\r", "\n"); 
		$conv_value		= str_replace($cr, "<br>", $value);
	}elseif($type==9){
		$conv_value		= str_replace("<br>", "\r\n", $value);
	}
	return $conv_value;
}
/****************************************
	メール受信解析処理
		パラメータ
			$mbox		: メール情報
			$mailno		: メール番号
		戻り値
			処理済みの値
			エラー時は未変換
*****************************************/
function  ReciveMail($mbox, $mailno){
	$mail=null;
	try{
		// ヘッダー情報の取得
		$head=imap_header($mbox, $mailno);
		$mail['head']=$head;
		// 受信日時の取得
		$mail['date'] = date('Y-m-d H:i:s', strtotime($head->date));
		// 送信者の取得
		$mhead=imap_mime_header_decode($head->from[0]->personal);
		foreach( $mhead as $key=>$value) {
			if( $value->charset != 'default' ) {
				$mail['personal']=mb_convert_encoding($value->text,'UTF-8',$value->charset);
			}else{
				$mail['personal']=$value->text;
			}
		}
		// アドレスの取得
		$mail['address']=$head->from[0]->mailbox.'@'.$head->from[0]->host;
		$mail['reply']=$head->reply_to[0]->mailbox.'@'.$head->reply_to[0]->host;
		// タイトルの有無
		if( !empty($head->subject) ) {
			// タイトルをデコード
			$mhead=imap_mime_header_decode($head->subject);
			foreach( $mhead as $key=>$value) {
				if( $value->charset != 'default' ) {
					$mail['subject']=mb_convert_encoding($value->text,'UTF-8',$value->charset);
				}else{
					$mail['subject']=$value->text;
				}
			}
		}else{
			// タイトルがない場合の処理を記述...
		}
		// 格納用変数の初期化
		$charset=null;
		$encoding=null;
		$attached_data=null;
		$parameters=null;
		// メール構造を取得
		$info=imap_fetchstructure($mbox, $mailno);
		if( !empty($info->parts) ) {
			// 
			$parts_cnt=count($info->parts);
			for( $p=0; $p<$parts_cnt; $p++ ) {
				// タイプにより処理を分ける
				// [参考] http://www.php.net/manual/ja/function.imap-fetchstructure.php
				if($info->parts[$p]->type == 0 ) {
					if( empty( $charset ) ) {
						$charset=$info->parts[$p]->parameters[0]->value;
					}
					if( empty( $encoding ) ) {
						$encoding=$info->parts[$p]->encoding;
					}
				}elseif(!empty($info->parts[$p]->parts) && $info->parts[$p]->parts[$p]->type == 0){
					if(!isset($info->parts[$p]->parameters)){
						$parameters=$info->parts[$p]->parameters[0]->value;
						if( empty( $charset ) ) {
							$charset=$info->parts[$p]->parts[$p]->parameters[0]->value;
						}
						if( empty( $encoding ) ) {
							$encoding=$info->parts[$p]->parts[$p]->encoding;
						}
					}
				}elseif($info->parts[$p]->type == 5){
					$files=imap_mime_header_decode($info->parts[$p]->dparameters[0]->value);
					if(!empty($files) && is_array($files) ) {
						$attached_data[$p]['file_name']=null;
						foreach($files as $key => $file) {
							if( $file->charset != 'default') {
								$attached_data[$p]['file_name'].=mb_convert_encoding($file->text, 'UTF-8', $file->charset);
							}else{
								$attached_data[$p]['file_name'].=$file->text;
							}
						}
					}
					$attached_data[$p]['content_type'] = $info->parts[$p]->subtype;
				}
			}
		}else{
			$charset=$info->parameters[0]->value;
			$encoding=$info->encoding;
		}
		if( empty($charset) ) {
			// エラー処理を記述...
		}
		// 本文を取得
		$body=imap_fetchbody($mbox, $mailno, 1, FT_INTERNAL);
		$body=trim($body);
		if( !empty($body) ) {
			// タイプによってエンコード変更
			switch( $encoding ) {
				case 0 :
					$mail['body']=mb_convert_encoding($body, "UTF-8", $charset);
					break;
				case 1 :
					$encode_body=imap_8bit($body);
					$encode_body=imap_qprint($encode_body);
					$mail['body']=mb_convert_encoding($encode_body, "UTF-8", $charset);
					break;
				case 3 :
					$encode_body=imap_base64($body);
					$mail['body']=mb_convert_encoding($encode_body, "UTF-8", $charset);
					break;
				case 4 :
					$encode_body=imap_qprint($body);
					$mail['body']=mb_convert_encoding($encode_body, 'UTF-8', $charset);
					break;
				case 2 :
				case 5 :
				default:
					// エラー処理を記述...
					break;
			}
		}else{
			// エラー処理を記述...
		}
		// 添付を取得
		if(!empty($attached_data)){
			foreach( $attached_data as $key => $value) {
				$attached=imap_fetchbody($mbox, $mailno, $key+1, FT_INTERNAL);
				if(empty($attached)) break;
				// ファイル名を一意の名前にする(同じファイルが存在しないように)
				list($name, $ex)=explode('.', $value['file_name']);
				$mail['attached_file'][$key]['file_name']=$name.'_'.time().'_'.$key.'.'.$ex;
				$mail['attached_file'][$key]['image']=imap_base64($attached);
				$mail['attached_file'][$key]['content_type']='Content-type: image/'.strtolower($value['content_type']);
			}
		}
	}catch(\Exception $e){
		$mail['message'] = $e->getMessage();
	}
	return $mail;
}
/****************************************
	メール本文解析処理
		パラメータ
			$dbh				: データベース接続
			$company_code		: 会社コード
			$recive_code		: 受信コード
			$mail_code		: 受信メールコード
			$context			: 取得した本文
		戻り値
			処理済みの値
			エラー時は未変換
*****************************************/
function DetailAnalysis($dbh, $company_code, $recive_code, $mail_code, $context){
	$analysis=null;
	$debug_msg=null;
	$ip				= $_SERVER["REMOTE_ADDR"] ;
	$login_code		= intval($_SESSION['member_code']);
	try{
		//ログ処理
		InsertLog($dbh, 'Info', $login_code, 'DetailAnalysis', '>メール本文解析処理　パラメータ　会社コード['.$company_code.']　受信コード['.$recive_code.']　受信メールコード['.$mail_code.']');
		//変換用項目を取得する
		$sql  = "SELECT ";
		$sql .= "	 RR.ITEM_CODE ";
		$sql .= "	,RM.ITEM_NAME ";
		$sql .= "	,RR.ITEM_TYPE ";
		$sql .= "	,RR.ITEM_REGEXP ";
		$sql .= "FROM ";
		$sql .= "		" . DBPRE . "M_RECIVE_REGEXP AS RR ";
		$sql .= "			INNER JOIN " . DBPRE . "M_RECIVE_ITEM AS RM ";
		$sql .= "				ON	RR.COMPANY_CODE	= RM.COMPANY_CODE ";
		$sql .= "				AND	RR.RECIVE_CODE	= RM.RECIVE_CODE ";
		$sql .= "				AND	RR.ITEM_CODE		= RM.ITEM_CODE ";
		$sql .= "WHERE ";
		$sql .= "		RR.COMPANY_CODE	= :company_code ";
		$sql .= "AND	RR.RECIVE_CODE	= :recive_code ";
		$sql .= "AND	RR.MAIL_CODE		= :mail_code ";
		$sql .= "";
		$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(':company_code', $company_code, PDO::PARAM_INT);
		$stmt->bindParam(':recive_code', $recive_code, PDO::PARAM_INT);
		$stmt->bindParam(':mail_code', $mail_code, PDO::PARAM_INT);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			//ITEM_CODE（項目コード）
			$item_db_code			= intval($row[0]);
			//ITEM_NAME（項目名）
			$item_db_name			= $row[1];
			//ITEM_TYPE（取得タイプ）0:未設定　1:対象文字の次の行　2:正規表現の値　3:対象行　4:最終行
			$item_db_type			= intval($row[2]);
			//ITEM_REGEXP（正規表現文字）
			$item_db_regexp		= $row[3];
			$item[$item_db_code]['CODE']		= $item_db_code;
			$item[$item_db_code]['ITEM']		= 'ITEM';
			$item[$item_db_code]['NAME']		= $item_db_name;
			$item[$item_db_code]['TYPE']		= $item_db_type;
			$item[$item_db_code]['REGEXP']	= $item_db_regexp;
		}
		$context_array = explode("<br>", $context); // とりあえず行に分割
		$context_array = array_map("trim", $context_array); // 各行にtrim()をかける
		$context_array = array_values($context_array); // これはキーを連番に振りなおしてるだけ
		for ($row=0; $row<count($context_array); $row++) {
			//解析用の変換処理
			$target=trim($context_array[$row]);
			$debug_msg[$row]['title']="(".intval($row+1)."行目)[".$target."]";
			//解析項目を利用する
			for($i=1; $i<=ITEM_COUNT; $i++){
				//値が存在しない場合は次の項目
				if(intval($item[$i]['CODE'])==0){
					continue;
				}
				//ここから解析処理
				$match=null;
				switch (intval($item[$i]['TYPE'])) {
					case 0:	//0:未設定
						break;
					case 1:	//1:対象文字の次の行
						if($target==$item[$i]['REGEXP']){
							$debug_msg[$row]['ITEM'.$i]['TYPE']="1:対象文字の次の行 [".$context_array[$row+1]."]";
							$debug_msg[$row]['ITEM'.$i]['VALUE']=$context_array[$row+1];
							$analysis['ITEM'.$item[$i]['CODE']]['NAME']=$item[$i]['NAME'];
							$analysis['ITEM'.$item[$i]['CODE']]['VALUE']=$context_array[$row+1];
						}
						break;
					case 2:	//2:正規表現の値
						$regexp="/^".str_replace('{'.$item[$i]['ITEM'].'}', '(?<'.$item[$i]['ITEM'].'>.*?)', $item[$i]['REGEXP'])."$/";
						preg_match($regexp, $target, $match, PREG_OFFSET_CAPTURE);
						if($match[$item[$i]['ITEM']][0]>""){
							$debug_msg[$row]['ITEM'.$i]['NAME']=$item[$i]['NAME'];
							$debug_msg[$row]['ITEM'.$i]['TYPE']="2:正規表現の値 [".$regexp."]";
							$debug_msg[$row]['ITEM'.$i]['VALUE']=$match[$item[$i]['ITEM']][0];
							$analysis['ITEM'.$item[$i]['CODE']]['NAME']=$item[$i]['NAME'];
							$analysis['ITEM'.$item[$i]['CODE']]['VALUE']=$match[$item[$i]['ITEM']][0];
						}
						break;
					case 3:	//3:対象行
						if(intval($item[$i]['REGEXP'])==intval($row+1)){
							$debug_msg[$row]['ITEM'.$i]['NAME']=$item[$i]['NAME'];
							$debug_msg[$row]['ITEM'.$i]['TYPE']="3:対象行 ".intval($item[$i]['REGEXP'])."行目";
							$debug_msg[$row]['ITEM'.$i]['VALUE']=$target;
							$analysis['ITEM'.$item[$i]['CODE']]['NAME']=$item[$i]['NAME'];
							$analysis['ITEM'.$item[$i]['CODE']]['VALUE']=$target;
						}
						break;
					case 4:	//4:最終行
						if(intval($row+1)==count($context_array)){
							$regexp="/^".str_replace('{'.$item[$i]['ITEM'].'}', '(?<'.$item[$i]['ITEM'].'>.*?)', $item[$i]['REGEXP'])."$/";
							preg_match($regexp, $target, $match, PREG_OFFSET_CAPTURE);
							if($match[$item[$i]['ITEM']][0]>""){
								$debug_msg[$row]['ITEM'.$i]['NAME']=$item[$i]['NAME'];
								$debug_msg[$row]['ITEM'.$i]['TYPE']="4:最終行 [".$regexp."]";
								$debug_msg[$row]['ITEM'.$i]['VALUE']=$match[$item[$i]['ITEM']][0];
								$analysis['ITEM'.$item[$i]['CODE']]['NAME']=$item[$i]['NAME'];
								$analysis['ITEM'.$item[$i]['CODE']]['VALUE']=$match[$item[$i]['ITEM']][0];
							}
						}
						break;
				}
			}
		}
	}catch(\Exception $e){
		$analysis['message'] = $e->getMessage();
	}
	$analysis['debug'] = $debug_msg;
	return $analysis;
}
?>