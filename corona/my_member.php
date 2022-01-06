<?php
function DispScript(){
	
	try{
		echo "<script type='text/javascript'>";
		//チェック処理
		echo "	function checkedit(){";
		echo "		rtn=true;";
		echo "		msg='';";
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
		//メールアドレス
		echo "		msg=runCheck($('#member_mail').val(), 'メールアドレス', 'nin', $('#member_mail').attr('maxlength'), 'char', 'reg', /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|shop|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i);";
		echo "		if(msg!=''){";
		echo "			$('#lbl_member_mail').html(msg);";
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
		echo "							 Type			: 8";
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
		echo "							window.location = 'index.php?d=my_member';";
		echo "						}";
		echo "					}).fail(function(data, textStatus, errorThrown){";
		echo "						alert('処理が異常終了しました');";
		echo "						rtn=false;";
		echo "					});";
		echo "				}";
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
	global $MY;
	global $AUTHORITY;

	try{
		$member_code		= $MY->member['code'];
		//ユーザー情報取得
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
		$sql .= "	M.MEMBER_CODE = :member_code ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(':member_code', $member_code, PDO::PARAM_INT);
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
		echo "<table class='editable'>";
		echo "	<tbody>";
		echo "		<tr>";
		echo "			<th>コード</th>";
		echo "			<td>";
		echo "				$member_code";
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
		echo "			<th>権限</th>";
		echo "			<td>";
		$code=0;
		foreach ($AUTHORITY as $name){
			if($code==$member_auth){
				echo "					$name";
			}
			$code += 1;
		}
		echo "			</td>";
		echo "		<tr>";
		echo "			<th>メールアドレス</th>";
		echo "			<td>";
		echo "				<input type='text' name='member_mail' id='member_mail' value='$member_mail' maxlength='50'>";
		echo "				<label id='lbl_member_mail' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>会社</th>";
		echo "			<td>";
		//会社情報取得
		$sql  = "SELECT ";
		$sql .= "	 C.COMPANY_NAME ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "M_COMPANY AS C ";
		$sql .= "WHERE ";
		$sql .= "	C.COMPANY_CODE = ? ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(1, $company_code, PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
			$company_name = $row[0];
		}
		echo "				$company_name<input type='hidden' name='company_code' id='company_code' value='$company_code'>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tbody>";
		echo "	<tfoot>";
		echo "		<tr>";
		echo "			<td>";
		echo "				<input type='hidden' name='' id='member_code' value='$member_code'>";
		echo "				<input type='hidden' name='' id='member_auth' value='$member_auth'>";
		echo "				<input type='button' name='regist' id='regist' alt='$type' value='登録'>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tfoot>";
		echo "</table>";
	}catch(Exception $ex){
		throw $ex;
	}
}
?>