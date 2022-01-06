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

	global $CMN;
	global $MY;
	global $STATUS_ARRAY;
	try{
		$user_status		= $_GET['s'];
		$user_name			= mb_convert_kana($_GET['un'], 's', 'UTF-8');;
		$user_address		= urldecode($_GET['ua']);
		$line_alignment		= $_GET['l'];
		$MY->GET = $_GET;
		//ユーザー一覧情報の取得
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
		if($line_alignment!=""){
			if($line_alignment=="0"){
				$sql .= "AND	U.LINE_ID = ''";
			}else{
				$sql .= "AND	U.LINE_ID <> ''";
			}
		}
		if(count($user_status) == 0){
			$sql .= "AND	U.USER_STATUS NOT IN ('CLEAR', 'INBED')";
		}else{
			$sql .= "AND (";
			$or="";
			$cnt=1;
			foreach ($user_status as $val){
				$sql .= $or . "	 U.USER_STATUS = '$val' ";
				$or="OR";
				$cnt+=1;
			}
			$sql .= "	) ";
		}
		if($user_name!=""){
			$sql .= "AND (";
			$or="";
			$cnt=1;
			foreach (preg_split("/[\s]/", $user_name) as $val){
				$sql .= $or . "	 U.USER_NAME		like :user_name$cnt ";
				$or="AND";
				$cnt+=1;
			}
			$sql .= "	) ";
		}
		if($user_address!=""){
			$sql .= "AND (";
			$or="";
			$cnt=1;
			foreach (preg_split("/[\s]/", $user_address) as $val){
				$sql .= $or . "	 U.USER_ADDRESS		LIKE :user_address$cnt ";
				$or="AND";
				$cnt+=1;
			}
			$sql .= "	) ";
		}
		$sql .= "ORDER BY ";
		$sql .= "	U.LAST_ACCESS ASC";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(":company_code", $MY->member["company"], PDO::PARAM_INT);
		if($user_name!=""){
			$cnt=1;
			foreach (preg_split("/[\s]/", $user_name) as $val){
				$stmt->bindParam(":user_name$cnt", sprintf('%%%s%%', addcslashes($val, '\_%')), PDO::PARAM_STR);
				$cnt+=1;
			}
		}
		if($user_address!=""){
			$cnt=1;
			foreach (preg_split("/[\s]/", $user_address) as $val){
				$stmt->bindParam(":user_address$cnt", sprintf('%%%s%%', addcslashes($val, '\_%')), PDO::PARAM_STR);
				$cnt+=1;
			}
		}
		$stmt->execute();
		echo "<div id='search'>";
		echo "	<form method='get'>";
		echo "		<input type='hidden' name='d' id='d' value='contact' >";
		echo "		<table class='searchtable'>";
		echo "			<tr>";
		echo "				<th>状態</th>";
		echo "				<td>";
		foreach($STATUS_ARRAY as $key => $value) {
			if(in_array($key, $user_status)){
				$check = "CHECKED";
			}else{
				$check = "";
			}
			echo "					<label><input type='checkbox' name='s[]' value='$key' $check>$value</label>";
		}
		echo "				</td>";
		echo "			</tr>";
		echo "			<tr>";
		echo "				<th>利用者名（部分一致）</th>";
		echo "				<td>";
		echo "					<input type='text' name='un' value='$user_name'>";
		echo "				</td>";
		echo "			</tr>";
		echo "			<tr>";
		echo "				<th>住所（部分一致）</th>";
		echo "				<td>";
		echo "					<input type='text' name='ua' value='$user_address'>";
		echo "				</td>";
		echo "			</tr>";
		echo "			<tr>";
		echo "				<th>LINE連携</th>";
		echo "				<td>";
		$check = $line_alignment=="" ? "CHECKED" : "";
		echo "					<label><input type='radio' name='l' value='' $check>すべて</label>";
		$check = $line_alignment=="0" ? "CHECKED" : "";
		echo "					<label><input type='radio' name='l' value='0' $check>未連携</label>";
		$check = $line_alignment=="1" ? "CHECKED" : "";
		echo "					<label><input type='radio' name='l' value='1' $check>連携済</label>";
		echo "				</td>";
		echo "			</tr>";
		echo "		</table>";
		echo "		<input type='submit' value='検索'>";
		echo "		<input type='reset' value='リセット'>";
		echo "	</form>";
		echo "</div>";
		echo "<div class='status_message'>";
		echo "	<div>状態にチェックがない場合は、未確認と自宅療養中が対象になります。</div>";
		echo "	　<div class='status_orange'>注：12時間以上経過(要注意)</div>";
		echo "	　<div class='status_red'>危：1日以上経過(早急にコンタクトを取ってください)</div>";
		echo "</div>";
		echo "<table class='listable'>";
		echo "	<thead>";
		echo "		<tr>";
		echo "			<th>処理</th>";
		echo "			<th>状態</th>";
		echo "			<th>名前</th>";
		echo "			<th class='mobile'>年齢</th>";
		echo "			<th class='mobile'>LINE連携</th>";
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
			//LINE IDが存在しない場合は、未連携　存在した場合は連携済
			if($line_id==""){
				$line_alignment = "";
			}else{
				$line_alignment = "連携済";
			}
			echo "		<tr>";
			echo "			<th>";
			echo "				<input type='button' class='detail' alt='$user_id' value='詳細'>";
			echo "			</th>";
			echo "			<th class='$status_class'>" . $STATUS_ARRAY[$status] . " ($access_val)</th>";
			echo "			<th>$user_name</th>";
			echo "			<th class='mobile'>$age 歳</th>";
			echo "			<th class='mobile'>$line_alignment</th>";
			echo "		</tr>";
			$cnt+=1;
		}
		echo "	</tbody>";
		echo "</table>";
	}catch(Exception $ex){
		throw $ex;
	}
}
?>