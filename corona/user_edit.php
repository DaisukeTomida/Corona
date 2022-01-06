<?php
function DispScript(){
	
	try{
		echo "<script type='text/javascript'>";
		//チェック処理
		echo "	function checkedit(){";
		echo "		rtn=true;";
		echo "		msg='';";
		//利用者名
		echo "		msg=runCheck($('#user_name').val(), '利用者名', 'his', $('#user_name').attr('maxlength'), 'byte', 'non', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_user_name').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//連絡先
		echo "		msg=runCheck($('#user_contact_no').val(), '連絡先', 'his', $('#user_contact_no').attr('maxlength'), 'byte', 'alpha', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_user_contact_no').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//住所
		echo "		msg=runCheck($('#user_address').val(), '住所', 'his', $('#user_address').attr('maxlength'), 'byte', 'non', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_user_address').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//年齢
		echo "		msg=runCheck($('#user_age').val(), '年齢', 'his', 3, 'byte', 'num', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_user_age').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//性別
		echo "		msg=runCheck($('#user_sex').val(), '性別', 'his', 0, 'non', 'non', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_user_sex').html(msg);";
		echo "			rtn=false;";
		echo "		}";

		//一つでもエラーがあった場合
		echo "		if(rtn==false){";
		echo "			alert('エラー項目があります\\n確認してください');";
		echo "		}";
		echo "		return rtn;";
		echo "	}";
		echo "	$(function(){";
		echo "		try{";
		//登録処理
		echo "			$('#regist').click(function(){";
		echo "				$('.error_msg').html('');";
		echo "				rtn=checkedit();";
		echo "				if(rtn==true&&confirm('登録しますか？')){";
		echo "					$.ajax({";
		echo "						 type			: 'POST'";
		echo "						,url			: './ajax/editUser.php'";
		echo "						,datatype		: 'html'";
		echo "						,data			: {";
		echo "							 type				: $(this).attr('alt')";
		echo "							,user_id			: $('#user_id').val()";
		echo "							,user_name			: $('#user_name').val()";
		echo "							,user_contact_no	: $('#user_contact_no').val()";
		echo "							,user_address		: $('#user_address').val()";
		echo "							,user_age			: $('#user_age').val()";
		echo "							,user_sex			: $('#user_sex').val()";
		echo "						 }";
		echo "						,cache		: false";
		echo "					}).done(function(data, textStatus, jqXHR){";
		echo "						if(data!=''){";
		echo "							alert(data);";
		echo "							rtn=false;";
		echo "						}else{";
		echo "							alert('処理が完了しました');";
		echo "							window.location = 'index.php?d=user';";
		echo "						}";
		echo "					}).fail(function(data, textStatus, errorThrown){";
		echo "						alert('処理が異常終了しました');";
		echo "						rtn=false;";
		echo "					});";
		echo "				}";
		echo "			});";
		//一覧処理
		echo "			$('#back').click(function(){";
		echo "				target=$(this).attr('alt');";
		echo "				window.location = 'index.php?d='+target;";
		echo "			});";
		echo "		}catch(e){";
		echo "			alert(e);";
		echo "		}";
		echo "	});";
		echo "</script>";

	}catch(Exception $ex){
		throw $ex;
	}
}
function DispBody(){

	global $CMN;
	global $SEX_ARRAY;
	global $STATUS_ARRAY;

	try{
		$user_id		= $_GET['c'];
		if($user_id==""){
			$user_id="";
			$type="1";
		}else{
			$type="2";
			//メンバー情報取得
			$sql  = "SELECT ";
			$sql .= "	  U.USER_ID ";
			$sql .= "	 ,U.LINE_ID ";
			$sql .= "	 ,U.USER_NAME ";
			$sql .= "	 ,U.USER_CONTACT_NO ";
			$sql .= "	 ,U.USER_ADDRESS ";
			$sql .= "	 ,U.USER_AGE ";
			$sql .= "	 ,U.USER_SEX ";
			$sql .= "	 ,U.LAST_ACCESS ";
			$sql .= "	 ,U.USER_STATUS ";
			$sql .= "FROM ";
			$sql .= "	" . DBPRE . "USER AS U ";
			$sql .= "WHERE ";
			$sql .= "	U.USER_ID = :user_id ";
			$sql .= "ORDER BY ";
			$sql .= "	U.LAST_ACCESS ";
			$sql .= "";
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam('user_id', $user_id, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
				$user_id = $row[0];							//USER_ID（ユーザーID）
				$line_id = $row[1];							//LINE_ID（LINEID）
				$user_name = $row[2];						//USER_NAME（ユーザー名）
				$user_contact_no = $row[3];					//USER_CONTACT_NO（連絡先）
				$user_address = $row[4];					//USER_ADDRESS（住所）
				$user_age = intval($row[5]);				//USER_AGE（年齢）
				$user_sex = $row[6];						//USER_SEX（性別）
				$last_access = $row[7];						//LAST_ACCESS（最終アクセス日時）
				$user_status = $row[8];						//USER_STATUS（状態）
			}
		}
		echo "<table class='editable'>";
		echo "	<tbody>";
		echo "		<tr>";
		echo "			<th>利用者コード<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<span id='span_user_id'>$user_id</span><input type='hidden' name='user_id' id='user_id' value='$user_id' maxlength='10'>";
		echo "				<label id='lbl_user_id' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>LINE ID</th>";
		echo "			<td>";
		if($line_id==""){
			echo "				未設定";
		}else{
			echo "				設定済";
		}
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>利用者名<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='text' name='user_name' id='user_name' value='$user_name' maxlength='20'>";
		echo "				<label id='lbl_user_name' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>連絡先<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='text' name='user_contact_no' id='user_contact_no' value='$user_contact_no' maxlength='20'>";
		echo "				<label id='lbl_user_contact_no' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>住所<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='text' name='user_address' id='user_address' value='$user_address' maxlength='40'>";
		echo "				<label id='lbl_user_address' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>年齢<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='number' name='user_age' id='user_age' value='$user_age'>";
		echo "				<label id='lbl_user_age' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>性別<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<label id='lbl_user_sex' class='error_msg hissu'></label>";
		echo "				<select name='user_sex' id='user_sex' >";
		foreach ($SEX_ARRAY as $key => $name){
			$select="";
			if($key==$user_sex){
				$select="selected";
			}
			echo "					<option value='$key' $select>$name</option>";
		}
		echo "				</select>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>最終アクセス日時</th>";
		echo "			<td>$last_access</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>状態</th>";
		echo "			<td>" . $STATUS_ARRAY[$user_status] . "</td>";
		echo "		</tr>";
		echo "	</tbody>";
		echo "	<tfoot>";
		echo "		<tr>";
		echo "			<td colspan='2'>";
		echo "				<input type='hidden' name='' id='user_id' value='$user_id'>";
		echo "				<input type='button' name='regist' id='regist' alt='$type' value='登録'>";
		echo "				<input type='button' name='back' id='back' alt='user' value='一覧'>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tfoot>";
		echo "</table>";
	}catch(Exception $ex){
		throw $ex;
	}
}
?>