<?php
function DispScript(){

	try{

		echo "<script type='text/javascript'>";
		//チェック処理
		echo "	function checkedit(){";
		echo "		rtn=true;";
		echo "		msg='';";
		//会社名
		echo "		msg=runCheck($('#company_name').val(), '会社名', 'his', $('#company_name').attr('maxlength'), 'byte', 'none', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_name').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//郵便番号
		echo "		msg=runCheck($('#company_post').val(), '郵便番号', 'nin', $('#company_post').attr('maxlength'), 'char', 'reg', /\d{3}-\d{4}/);";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_post').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//市区町村
		echo "		msg=runCheck($('#company_shi').val(), '市区町村', 'nin', $('#company_shi').attr('maxlength'), 'byte', 'all', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_shi').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//住所１
		echo "		msg=runCheck($('#company_add1').val(), '住所１', 'nin', $('#company_add1').attr('maxlength'), 'byte', 'none', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_add1').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//住所２
		echo "		msg=runCheck($('#company_add2').val(), '住所２', 'nin', $('#company_add2').attr('maxlength'), 'byte', 'none', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_add2').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//電話番号
		echo "		msg=runCheck($('#company_tel').val(), '電話番号', 'nin', $('#company_tel').attr('maxlength'), 'char', 'reg', /^[0-9-]{6,9}$|^[0-9-]{12}$/);";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_tel').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//ホームページ
		echo "		msg=runCheck($('#company_web').val(), 'ホームページ', 'nin', $('#company_web').attr('maxlength'), 'char', 'reg', /^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/);";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_web').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//メールアドレス
		echo "		msg=runCheck($('#company_mail').val(), 'メールアドレス', 'nin', $('#company_mail').attr('maxlength'), 'char', 'reg', /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|shop|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i);";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_mail').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//ホスト名
		echo "		msg=runCheck($('#company_host').val(), 'ホスト名', 'his', $('#company_mail').attr('maxlength'), 'byte', 'non', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_host').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//ポート番号
		echo "		msg=runCheck($('#company_port').val(), 'ポート番号', 'his', $('#company_port').attr('maxlength'), 'char', 'num', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_port').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//ユーザーＩＤ
		echo "		msg=runCheck($('#company_id').val(), 'コード', 'his', $('#company_id').attr('maxlength'), 'char', 'alpha', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_id').html(msg);";
		echo "			rtn=false;";
		echo "		}";
		//パスワード
		echo "		msg=runCheck($('#company_pass').val(), 'パスワード', 'his', $('#company_pass').attr('maxlength'), 'char', 'non', '');";
		echo "		if(msg!=''){";
		echo "			$('#lbl_company_pass').html(msg);";
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
		echo "					companyCode=$('#company_code').val();";
		echo "					companyName=$('#company_name').val();";
		echo "					companyPost=$('#company_post').val();";
		echo "					companyKen=$('#company_ken').val();";
		echo "					companyShi=$('#company_shi').val();";
		echo "					companyAdd1=$('#company_add1').val();";
		echo "					companyAdd2=$('#company_add2').val();";
		echo "					companyTel=$('#company_tel').val();";
		echo "					companyWeb=$('#company_web').val();";
		echo "					companyMail=$('#company_mail').val();";
		echo "					companyHost=$('#company_host').val();";
		echo "					companyPort=$('#company_port').val();";
		echo "					companyId=$('#company_id').val();";
		echo "					companyPass=$('#company_pass').val();";
		echo "					$.ajax({";
		echo "						 type			: 'POST'";
		echo "						,url			: './ajax/editCompany.php'";
		echo "						,datatype		: 'html'";
		echo "						,data			: {";
		echo "							 Type				: type";
		echo "							,CompanyCode		: companyCode";
		echo "							,CompanyName		: companyName";
		echo "							,CompanyPost		: companyPost";
		echo "							,CompanyKen		: companyKen";
		echo "							,CompanyShi		: companyShi";
		echo "							,CompanyAdd1		: companyAdd1";
		echo "							,CompanyAdd2		: companyAdd2";
		echo "							,CompanyTel		: companyTel";
		echo "							,CompanyWeb		: companyWeb";
		echo "							,CompanyMail		: companyMail";
		echo "							,CompanyHost		: companyHost";
		echo "							,CompanyPort		: companyPort";
		echo "							,CompanyId		: companyId";
		echo "							,CompanyPass		: companyPass";
		echo "						 }";
		echo "						,cache		: false";
		echo "					}).done(function(data, textStatus, jqXHR){";
		echo "						if(data!=''){";
		echo "							alert(data);";
		echo "							rtn=false;";
		echo "						}else{";
		echo "							alert('処理が完了しました');";
		echo "							window.location = 'index.php?d=company';";
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

	global $MY;
	global $CMN;
	
	try{

		$company_code		= $_GET['c'];
		if($company_code==0){
			$company_code="";
			$type="1";
		}else{
			$type="2";
			//会社情報取得
			$sql  = "SELECT ";
			$sql .= "	  C.COMPANY_CODE ";
			$sql .= "	 ,C.COMPANY_NAME ";
			$sql .= "	 ,C.COMPANY_POST ";
			$sql .= "	 ,C.COMPANY_KEN ";
			$sql .= "	 ,C.COMPANY_SHI ";
			$sql .= "	 ,C.COMPANY_ADDRESS1 ";
			$sql .= "	 ,C.COMPANY_ADDRESS2 ";
			$sql .= "	 ,C.COMPANY_TEL ";
			$sql .= "	 ,C.COMPANY_WEB ";
			$sql .= "	 ,C.COMPANY_MAIL ";
			$sql .= "	 ,C.COMPANY_HOST ";
			$sql .= "	 ,C.COMPANY_PORT ";
			$sql .= "	 ,C.COMPANY_ID ";
			$sql .= "	 ,C.COMPANY_PASS ";
			$sql .= "FROM ";
			$sql .= "	" . DBPRE . "M_COMPANY AS C ";
			$sql .= "WHERE ";
			$sql .= "	  C.COMPANY_CODE	= :company_code ";
			$sql .= "";
			$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
			$stmt->bindParam(':company_code', $company_code, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
				$company_name = $row[1];					//COMPANY_NAME（会社名）
				$company_post = $row[2];					//COMPANY_POST（会社郵便番号）
				$company_ken = $row[3];						//COMPANY_KEN（会社都道府県）
				$company_shi = $row[4];						//COMPANY_SHI（会社市区町村）
				$company_add1 = $row[5];					//COMPANY_ADDRESS１（会社住所１）
				$company_add2 = $row[6];					//COMPANY_ADDRESS２（会社住所２）
				$company_tel = $row[7];						//COMPANY_TEL（会社電話番号）
				$company_web = $row[8];						//COMPANY_WEB（会社ホームページ）
				$company_mail = $row[9];					//COMPANY_MAIL（会社メールアドレス）
				$company_host = $row[10];					//COMPANY_HOST（会社送信ホスト）
				$company_port = $row[11];					//COMPANY_PORT（会社送信ポート）
				$company_id = $row[12];						//COMPANY_ID（会社送信ユーザー）
				$company_pass = ConvSecret($row[13], 9);	//COMPANY_PASS（会社送信パスワード）
			}
		}

		echo "<table class='editable'>";
		echo "	<tbody>";
		echo "		<tr>";
		echo "			<th>名称<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='text' name='company_name' id='company_name' value='$company_name' maxlength='30'>";
		echo "				<label id='lbl_company_name' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>郵便番号</th>";
		echo "			<td>";
		echo "				<input type='text' name='company_post' id='company_post' value='$company_post' maxlength='10'>";
		echo "				<label id='lbl_company_post' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>都道府県</th>";
		echo "			<td>";
		if(getCode("KEN", "")==false){
			echo $CMN->code->error_message;
		}else{
			echo "				<select name='company_ken' id='company_ken' >";
			echo "					<option value=''></option>";
			foreach($CMN->code->row as $item){
				//CODE（コード）
				$code = $item->db_code;
				//NAME（名称）
				$name = $item->db_name;
				$select="";
				if($name==$company_ken){
					$select="selected";
				}
				echo "					<option value='$name' $select>$name</option>";
			}
			echo "				</select>";
			echo "				<label id='lbl_company_ken' class='error_msg hissu'></label>";
		}
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>市区町村</th>";
		echo "			<td>";
		echo "				<input type='text' name='company_shi' id='company_shi' value='$company_shi' maxlength='20'>";
		echo "				<label id='lbl_company_shi' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>住所１</th>";
		echo "			<td>";
		echo "				<input type='text' name='company_add1' id='company_add1' value='$company_add1' maxlength='50'>";
		echo "				<label id='lbl_company_add1' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>住所２</th>";
		echo "			<td>";
		echo "				<input type='text' name='company_add2' id='company_add2' value='$company_add2' maxlength='50'>";
		echo "				<label id='lbl_company_add2' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>電話番号</th>";
		echo "			<td>";
		echo "				<input type='text' name='company_tel' id='company_tel' value='$company_tel' maxlength='15'>";
		echo "				<label id='lbl_company_tel' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>ホームページ</th>";
		echo "			<td>";
		echo "				<input type='text' name='company_web' id='company_web' value='$company_web' maxlength='100'>";
		echo "				<label id='lbl_company_web' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>メールアドレス</th>";
		echo "			<td>";
		echo "				<input type='text' name='company_mail' id='company_mail' value='$company_mail' maxlength='50'>";
		echo "				<label id='lbl_company_mail' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>ホスト名<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='text' name='company_host' id='company_host' value='$company_host' maxlength='50'>";
		echo "				<label id='lbl_company_host' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>ポート番号<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='number' name='company_port' id='company_port' value='$company_port' maxlength='4'>";
		echo "				<label id='lbl_company_port' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>ユーザーＩＤ<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='text' name='company_id' id='company_id' value='$company_id' maxlength='50'>";
		echo "				<label id='lbl_company_id' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>パスワード<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='password' name='company_pass' id='company_pass' value='$company_pass' maxlength='20'>";
		echo "				<label id='lbl_company_pass' class='error_msg hissu'></label>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tbody>";
		echo "	<tfoot>";
		echo "		<tr>";
		echo "			<td colspan='2'>";
		echo "				<input type='hidden' name='' id='company_code' value='$company_code'>";
		echo "				<input type='button' name='regist' id='regist' alt='$type' value='登録'>";
		echo "				<input type='button' name='back' id='back' alt='company' value='一覧'>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tfoot>";
		echo "</table>";

	}catch(Exception $ex){
		throw $ex;
	}

}
?>