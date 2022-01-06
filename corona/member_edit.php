<?php
function DispScript(){
	
	try{
		echo "<script type='text/javascript'>";
		//チェック処理
		echo "	function checkedit(){";
		echo "		rtn=true;";
		echo "		msg='';";
		//コード
		echo "		msg=runCheck($('#member_code').val(), 'コード', 'his', $('#member_code').attr('maxlength'), 'char', 'alpha', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_member_code').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//パスワード
		echo "		msg=runCheck($('#member_pass').val(), 'パスワード', 'his', $('#member_pass').attr('maxlength'), 'char', 'reg', /^(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,20}$/i);";
		echo "		if(msg!=''){";
		echo "			$('#lbl_member_pass').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//名称
		echo "		msg=runCheck($('#member_name').val(), '名称', 'his', $('#member_name').attr('maxlength'), 'byte', 'non', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_member_name').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//権限
		echo "		msg=runCheck($('#member_auth').val(), '権限', 'his', 0, 'non', 'reg', /[1-9]/);";
		echo "		if(msg!=''){";
		echo "			$('#lbl_member_auth').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//メールアドレス
		echo "		msg=runCheck($('#member_mail').val(), 'メールアドレス', 'nin', $('#member_mail').attr('maxlength'), 'char', 'reg', /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|shop|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i);";
		echo "		if(msg!=''){";
		echo "			$('#lbl_member_mail').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//会社
		echo "		msg=runCheck($('#company_code').val(), '会社', 'his', 0, 'non', 'non', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_code').html(msg);";
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
		echo "					type=$(this).attr('alt');";
		echo "					memberCode=$('#member_code').val();";
		echo "					memberPass=$('#member_pass').val();";
		echo "					memberName=$('#member_name').val();";
		echo "					memberMail=$('#member_mail').val();";
		echo "					memberAuth=$('#member_auth').val();";
		echo "					companyCode=$('#company_code').val();";
		echo "					$.ajax({";
		echo "						 type			: 'POST'";
		echo "						,url			: './ajax/editMember.php'";
		echo "						,datatype		: 'html'";
		echo "						,data			: {";
		echo "							 Type			: type";
		echo "							,MemberCode		: memberCode";
		echo "							,MemberPass		: memberPass";
		echo "							,MemberName		: memberName";
		echo "							,MemberMail		: memberMail";
		echo "							,MemberAuth		: memberAuth";
		echo "							,CompanyCode	: companyCode";
		echo "						 }";
		echo "						,cache		: false";
		echo "					}).done(function(data, textStatus, jqXHR){";
		echo "						if(data!=''){";
		echo "							alert(data);";
		echo "							rtn=false;";
		echo "						}else{";
		echo "							alert('処理が完了しました');";
		echo "							window.location = 'index.php?d=member';";
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
	global $AUTHORITY;

	try{
		$member_code		= $_GET['c'];
		if($member_code==""){
			$member_code="";
			$type="1";
		}else{
			$type="2";
			//メンバー情報取得
			$sql  = "SELECT ";
			$sql .= "	 M.COMPANY_CODE ";
			$sql .= "	,M.MEMBER_CODE ";
			$sql .= "	,M.MEMBER_PASS ";
			$sql .= "	,M.MEMBER_NAME ";
			$sql .= "	,M.MEMBER_MAIL ";
			$sql .= "	,M.MEMBER_AUTHORITY ";
			$sql .= "	,M.MEMBER_ENABLE ";
			$sql .= "FROM ";
			$sql .= "	" . DBPRE . "M_MEMBER AS M ";
			$sql .= "WHERE ";
			$sql .= "	M.MEMBER_CODE = ? ";
			$sql .= "";
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam(1, $member_code, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
				$company_code = $row[0];				//COMPANY_CODE（会社コード）
				$member_code = $row[1];					//MEMBER_CODE（ユーザーコード）
				$member_pass = ConvSecret($row[2], 9);	//MEMBER_PASS（パスワード）
				$member_name = $row[3];					//MEMBER_NAME（ユーザー名）
				$member_mail = $row[4];					//MEMBER_MAIL（ユーザーメールアドレス）
				$member_auth = $row[5];					//MEMBER_AUTHORITY（ユーザー権限）
				$member_enable = intval($row[6]);		//MEMBER_ENABLE （ユーザー有効フラグ）
			}
		}
		echo "<table class='editable'>";
		echo "	<tbody>";
		echo "		<tr>";
		echo "			<th>コード<span class='hissu'>*</span></th>";
		echo "			<td>";
		if($type=="1"){
			echo "				<input type='text' name='member_code' id='member_code' value='$member_code' maxlength='10'>";
		}else{
			echo "				$member_code<input type='hidden' name='member_code' id='member_code' value='$member_code' maxlength='10'>";
		}
		echo "				<label id='lbl_member_code' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>パスワード<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<label class='explain'>半角英数字をそれぞれ1種類以上含む6文字以上20文字以下</label>";
		echo "				<input type='password' name='member_pass' id='member_pass' value='$member_pass' maxlength='20'>";
		echo "				<label id='lbl_member_pass' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>名称<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='text' name='member_name' id='member_name' value='$member_name' maxlength='20'>";
		echo "				<label id='lbl_member_name' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>権限<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<select name='member_auth' id='member_auth' >";
		$code=0;
		foreach ($AUTHORITY as $name){
			$select="";
			if($code==$member_auth){
				$select="selected";
			}
			echo "					<option value='$code' $select>$name</option>";
			$code += 1;
		}
		echo "				</select><br>";
		echo "				<label id='lbl_member_auth' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>メールアドレス</th>";
		echo "			<td>";
		echo "				<input type='text' name='member_mail' id='member_mail' value='$member_mail' maxlength='50'>";
		echo "				<label id='lbl_member_mail' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>会社<span class='hissu'>*</span></th>";
		echo "			<td>";
		$sql  = "SELECT ";
		$sql .= "	 C.COMPANY_CODE ";
		$sql .= "	,C.COMPANY_NAME ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "M_COMPANY AS C ";
		$sql .= "ORDER BY ";
		$sql .= "	C.COMPANY_CODE ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->execute();
		echo "				<select name='company_code' id='company_code' >";
		echo "					<option value=''></option>";
		while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			//CODE（コード）
			$code = $row[0];
			//NAME（名称）
			$name = $row[1];
			$select="";
			if($code==$company_code){
				$select="selected";
			}
			echo "					<option value='$code' $select>$name</option>";
		}
		echo "				</select><br>";
		echo "				<label id='lbl_company_code' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tbody>";
		echo "	<tfoot>";
		echo "		<tr>";
		echo "			<td colspan='2'>";
		echo "				<input type='hidden' name='' id='member_code' value='$member_code'>";
		echo "				<input type='button' name='regist' id='regist' alt='$type' value='登録'>";
		echo "				<input type='button' name='back' id='back' alt='member' value='一覧'>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tfoot>";
		echo "</table>";
	}catch(Exception $ex){
		throw $ex;
	}
}
?>