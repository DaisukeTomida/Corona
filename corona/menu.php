<?php
function DispScript(){

	try{
		echo "<script type='text/javascript'>";
		echo "	$(function(){";
		//登録処理
		echo "		$('.detail').click(function(){";
		echo "			target=$(this).attr('alt');";
		echo "			window.location = 'index.php?d=contact_edit&c='+target;";
		echo "		});";
		echo "	});";
		echo "</script>";
	}catch(Exception $ex){
		throw $ex;
	}

}
function DispBody(){

	global $MY;
	global $CMN;
	global $STATUS_ARRAY;

	try{

		if(date("H")>=3&&date("H")<12){
			$hello="おはようございます。";
		}else	if(date("H")>=12&&date("H")<19){
			$hello="こんにちは。";
		}else{
			$hello="こんばんは。";
		}
		echo $hello . $MY->member["name"] . "さん。<br>";
		$sql  = "SELECT ";
		$sql .= "	  U.USER_ID ";
		$sql .= "	 ,U.LINE_ID ";
		$sql .= "	 ,U.COMPANY_CODE ";
		$sql .= "	 ,U.USER_NAME ";
		$sql .= "	 ,U.USER_CONTACT_NO ";
		$sql .= "	 ,U.USER_ADDRESS ";
		$sql .= "	 ,U.USER_AGE ";
		$sql .= "	 ,U.USER_SEX ";
		$sql .= "	 ,U.LAST_ACCESS ";
		$sql .= "	 ,U.USER_STATUS ";
		$sql .= "	 ,(DATEDIFF(CURRENT_TIMESTAMP(), U.LAST_ACCESS))	AS ACCESS_VAL_DAY ";
		$sql .= "	 ,TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP(), U.LAST_ACCESS))	AS ACCESS_VAL_TIME ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "USER AS U ";
		$sql .= "WHERE ";
		$sql .= "	 U.COMPANY_CODE		= :company_code ";
		$sql .= "AND	U.USER_STATUS NOT IN ('CLEAR', 'INBED') ";
		$sql .= "AND	DATEDIFF(CURRENT_TIMESTAMP(), U.LAST_ACCESS) > 0 ";
		$sql .= "ORDER BY ";
		$sql .= "	U.LAST_ACCESS ASC ";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(":company_code", $MY->member["company"], PDO::PARAM_INT);
		$stmt->execute();
		echo "<div class='status_message'>";
		echo "	<div>状態にチェックがない場合は、未確認と自宅療養中が対象になります。</div>";
		echo "	<div class='status_red'>危：1日以上経過(早急にコンタクトを取ってください)</div>";
		echo "</div>";
		echo "<table class='listable'>";
		echo "	<thead>";
		echo "		<tr>";
		echo "			<th>処理</th>";
		echo "			<th>状態</th>";
		echo "			<th>名前</th>";
		echo "			<th class='mobile'>年齢</th>";
		echo "		</tr>";
		echo "	</thead>";
		echo "	<tbody>";
		$cnt=1;
		while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			$user_id = $row[0];						//USER_ID（ユーザーID）
			$line_id = $row[1];						//LINE_ID（LINEID）
			$company_code = $row[2];				//COMPANY_CODE（会社コード）
			$user_name = $row[3];					//USER_NAME（ユーザー名）
			$contact_no = $row[4];					//CONTACT_NO（連絡先）
			$address = $row[5];						//ADDRESS（住所）
			$age = intval($row[6]);					//AGE（年齢）
			$sex = $row[7];							//SEX（性別）
			$last_access = $row[8];					//LAST_ACCESS（最終アクセス日時）
			$status = $row[9];						//STATUS（状態）
			$access_val_day = $row[10];				//ACCESS_VAL_DAY
			$access_val_time = $row[11];			//ACCESS_VAL_TIME
			if($access_val_day==""){
				$access_val = intval($access_val_time / 3600) . "時間経過";
			}else{
				$access_val = $access_val_day . "日連絡なし";
			}
			if($access_val_day != ""){
				$status_class = "status_red";
			}else if(intval($access_val_time / 3600)  > "12"){
				$status_class = "status_orange";
			}else{
				$status_class = "";
			}
			echo "		<tr>";
			echo "			<th>";
			echo "				<input type='button' class='detail' alt='$user_id' value='詳細'>";
			echo "			</th>";
			echo "			<th class='$status_class'>" . $STATUS_ARRAY[$status] . " ($access_val)</th>";
			echo "			<th>$user_name</th>";
			echo "			<th class='mobile'>$age 歳</th>";
			echo "		</tr>";
			$cnt+=1;
		}
		echo "	</tbody>";
		echo "</table>";
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
		$CMN->log->error("システムエラー:" . $CMN->error);
		echo $CMN->error;
		throw $ex;
	}

}
?>