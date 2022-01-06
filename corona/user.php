<?php
function DispScript(){
	
	try{
		echo "<script type='text/javascript'>";
		echo "	$(function(){";
		//登録処理
		echo "		$('.edit').click(function(){";
		echo "			target=$(this).attr('alt');";
		echo "			window.location = 'index.php?d=user_edit&c='+target;";
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
		$sql .= "	 ,C.COMPANY_NAME ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "USER AS U ";
		$sql .= "		LEFT JOIN " . DBPRE . "M_COMPANY AS C ";
		$sql .= "			ON	U.COMPANY_CODE = C.COMPANY_CODE ";
		$sql .= "WHERE ";
		$sql .= "	 U.COMPANY_CODE		= :company_code ";
		$sql .= "ORDER BY ";
		$sql .= "	U.LAST_ACCESS ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(":company_code", $MY->member["company"], PDO::PARAM_INT);
		$stmt->execute();
		echo "<input type='button' class='edit' value='新規' alt=''>";
		echo "<table class='listable'>";
		echo "	<thead>";
		echo "		<tr>";
		echo "			<th>処理</th>";
		echo "			<th class='mobile'>コード</th>";
		echo "			<th>名前</th>";
		echo "			<th>最終アクセス日時</th>";
		echo "			<th>状態</th>";
		echo "			<th class='mobile'>地区名</th>";
		echo "		</tr>";
		echo "	</thead>";
		echo "	<tbody>";
		$cnt=1;
		while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			$user_id = $row[0];						//USER_ID（ユーザーID）
			$line_id = $row[1];						//LINE_ID（LINEID）
			$company_code = $row[2];				//COMPANY_CODE（会社コード）
			$user_name = $row[3];	//USER_NAME（ユーザー名）
			$contact_no = $row[4];	//CONTACT_NO（連絡先）
			$address = $row[5];		//ADDRESS（住所）
			$age = intval($row[6]);				//AGE（年齢）
			$sex = $row[7];							//SEX（性別）
			$last_access = $row[8];					//LAST_ACCESS（最終アクセス日時）
			$status = $row[9];						//STATUS（状態）
			$company_name = $row[10];	//COMPANY_NAME（会社名）
			echo "		<tr>";
			echo "			<th class='alignCenter'>";
			echo "				<input type='button' class='edit' alt='$user_id' value='修正'>";
			echo "			</th>";
			echo "			<td class='mobile'>$user_id</td>";
			echo "			<th>$user_name</th>";
			echo "			<th>$last_access</th>";
			echo "			<th>" . $STATUS_ARRAY[$status] . "</th>";
			echo "			<td class='mobile'>$company_name</td>";
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