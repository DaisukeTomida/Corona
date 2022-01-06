<?php
function DispScript(){

	try{
		echo "<script type='text/javascript'>";
		echo "	$(function(){";
		echo "		try{";
		//閉じるボタン
		echo "          $(document).on('click', '#modal_close', function(){";
		echo "              modalClose();";
		echo "      	});";
		//登録ボタン
		echo "          $(document).on('click', '#modal_regist', function(){";
		echo "				var user_id = $('#user_id').val();";
		echo "				var edit_status = $('#modal_edit_status').val();";
		echo "				var message = $('#modal_message').val();";
		echo "				var msg = runCheck($('#modal_message').val(), 'メッセージ', 'his', $('#modal_message').attr('maxlength'), 'byte', 'non', '');";
		echo "				if(msg!=''){";
		echo "					$('#lbl_modal_message').html(msg);";
		echo "              	alert('メモは必須入力です\\r\\n詳しい状況等を入力してください');";
		echo "              }else if(confirm('登録しますか？')){";
		echo "              	update_message(user_id, edit_status, message);";
		echo "              }";
		echo "      	});";
		//情報入力
		echo "			$('#info').click(function(){";
		echo "				var html = '';";
		echo "				html = $('#copy').html();";
		echo "				html += '<table class=\'editable\'>';";
		echo "				html += '	<tbody>';";
		echo "				html += '		<tr>';";
		echo "				html += '			<th style=\'text-align:center;\'>メモ</th>';";
		echo "				html += '		</tr>';";
		echo "				html += '		<tr>';";
		echo "				html += '			<td>';";
		echo "				html += '				<input type=\'text\' id=\'modal_message\'  maxlength=\'100\' style=\'height:40px;vertical-align:middle;font-size:140%;\' value=\'\'>';";
		echo "				html += '				<label id=\'lbl_modal_message\' class=\'error_msg hissu\'></label>';";
		echo "				html += '			</td>';";
		echo "				html += '		</tr>';";
		echo "				html += '	</tbody>';";
		echo "				html += '</table>';";
		echo "				html += '<input type=\'button\' name=\'modal_regist\' id=\'modal_regist\' value=\'登録\'>';";
		echo "				html += '<input type=\'button\' name=\'modal_close\' id=\'modal_close\' value=\'閉じる\'>';";
		echo "      		modalOpen(html);";
		echo "				$('#modalForm #edit_status').attr('id', 'modal_edit_status');";
		echo "			});";
		//一覧処理
		echo "			$('#back').click(function(){";
		echo "				var d = $(this).attr('alt');";
		echo "				var s = '';";
		echo "				var un = '';";
		echo "				var ua = '';";
		echo "				var l = '';";
		echo "				if($('#s').val() != ''){";
		echo "					var user_status = $('#s').val();";
		echo "					var res = user_status.split(',');";
		echo "					$.each(res, function(index, value){";
		echo "						s = s + '&s[]=' + value;";
		echo "					});";
		echo "				};";
		echo "				if($('#un').val() != ''){";
		echo "					un = '&un=' + $('#un').val();";
		echo "				};";
		echo "				if($('#ua').val() != ''){";
		echo "					ua = '&ua=' + $('#ua').val();";
		echo "				};";
		echo "				if($('#l').val() != ''){";
		echo "					l = '&l=' + $('#l').val();";
		echo "				};";
		echo "				window.location = encodeURI('index.php?d=' + d + s + un + ua + l);";
		echo "			});";
		echo "			$('#edit_status').change(function(){";
		echo "				var rtn = false;";
		echo "				if(confirm('状態を変更しますか？')){";
		echo "					$.ajax({";
		echo "						 type			: 'POST'";
		echo "						,url			: './ajax/editContact.php'";
		echo "						,datatype		: 'html'";
		echo "						,data			: {";
		echo "							 type				: 'updateStatus'";
		echo "							,user_id			: $('#user_id').val()";
		echo "							,user_status_bef	: $(this).attr('alt')";
		echo "							,user_status		: $(this).val()";
		echo "						 }";
		echo "						,cache		: false";
		echo "					}).done(function(data, textStatus, jqXHR){";
		echo "						if(data!=''){";
		echo "							alert(data);";
		echo "						}else{";
		echo "							alert('処理が完了しました');";
		echo "							window.location.reload(true);";
		echo "							rtn = true;";
		echo "						}";
		echo "					}).fail(function(data, textStatus, errorThrown){";
		echo "						alert('処理が異常終了しました');";
		echo "					});";
		echo "				}";
		echo "				if(rtn == false){";
		echo "					$(this).val($(this).attr('alt'));";
		echo "				}";
		echo "			});";
		echo "		}catch(e){";
		echo "			alert(e);";
		echo "		}";
		//更新処理
		echo "		function update_message(user_id, edit_status, message){";
		echo "			try{";
		echo "				$.ajax({";
		echo "					 type			: 'POST'";
		echo "					,url			: './ajax/editContact.php'";
		echo "					,datatype		: 'html'";
		echo "					,data			: {";
		echo "						 type				: 'updateMessage'";
		echo "						,user_id			: user_id";
		echo "						,user_status		: edit_status";
		echo "						,message			: message";
		echo "					 }";
		echo "					,cache		: false";
		echo "				}).done(function(data, textStatus, jqXHR){";
		echo "					if(data!=''){";
		echo "						alert(data);";
		echo "					}else{";
		echo "						alert('処理が完了しました');";
		echo "						window.location.reload(true);";
		echo "					}";
		echo "				}).fail(function(data, textStatus, errorThrown){";
		echo "					alert('処理が異常終了しました');";
		echo "				});";
		echo "			}catch(e){";
		echo "				alert(e);";
		echo "			}";
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
	global $STATUS_ARRAY;
	global $ROUTE_ARRAY;

	try{
		$user_id			= $_GET['c'];
		$user_status		= $MY->GET['s'];
		$user_name			= mb_convert_kana($MY->GET['un'], 's', 'UTF-8');;
		$user_address		= urldecode($MY->GET['ua']);
		$line_alignment		= $MY->GET['l'];
		echo "<input type='hidden' id='s' value='" . join(",", $user_status) . "'>";
		echo "<input type='hidden' id='un' value='$user_name'>";
		echo "<input type='hidden' id='ua' value='$user_address'>";
		echo "<input type='hidden' id='l' value='$line_alignment'>";
		echo "<input type='hidden' id='user_id' value='$user_id'>";
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
		$sql .= "	 U.USER_ID		= :user_id ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			$row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
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
				$access_val = $access_val_day . "日間連絡なし";
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
		}
		echo "<input type='button' name='back' id='back' alt='contact' value='一覧へもどる'>";
		echo "<div id='copy'>";
		echo "	<p class='listable $status_class'  style='height:40px;width:100%;max-width:200px;font-size:140%;font-weight:bold;'>$access_val</p>";
		echo "	<table class='editable'>";
		echo "		<tbody>";
		echo "			<tr>";
		echo "				<th>利用者名</th>";
		echo "				<td colspan='3'>$user_name</td>";
		echo "			</tr>";
		echo "			<tr>";
		echo "				<th>年齢</th>";
		echo "				<td class='alignRight'>$age 歳</td>";
		echo "				<th>性別</th>";
		echo "				<td>$sex</td>";
		echo "			</tr>";
		echo "		</tbody>";
		echo "	</table>";
		echo "	<hr>";
		echo "	<table class='editable'>";
		echo "		<tbody>";
		echo "			<tr>";
		echo "				<th>状態</th>";
		echo "				<td style='height:40px;vertical-align:middle;font-size:140%;'>";
		echo "					<select alt='$status' name='edit_status' id='edit_status' style='height:40px;width:100%;font-size:140%;'>";
		foreach ($STATUS_ARRAY as $key => $name){
			$select="";
			if($key==$status){
				$select="selected";
			}
			echo "						<option value='$key' $select>$name</option>";
		}
		echo "					</select>";
		echo "				</td>";
		echo "			</tr>";
		echo "			<tr>";
		echo "				<th>連絡先</th>";
		echo "				<td style='height:40px;vertical-align:middle;font-size:140%;text-align:left;'>$contact_no</td>";
		echo "			</tr>";
		echo "			<tr>";
		echo "				<th>住所</th>";
		echo "				<td style='height:40px;vertical-align:middle;font-size:140%;text-align:left;'>$address</td>";
		echo "			</tr>";
		echo "		</tbody>";
		echo "	</table>";
		echo "</div>";
		echo "<div style='margin:6px 0;'>";
		echo "<input type='button' name='info' id='info' value='情報入力'>";
		if($status_class == "status_red"){
			echo "	　<span class='status_red'>1日以上経過(早急にコンタクトを取ってください)</span>";
		}
		echo "</div>";
		echo "<table class='listable'>";
		echo "	<thead>";
		echo "		<tr>";
		echo "			<th>処理日時</th>";
		echo "			<th>連絡箇所</th>";
		echo "			<th>メッセージ</th>";
		echo "		</tr>";
		echo "	</thead>";
		echo "	<tbody>";
		$sql  = "SELECT ";
		$sql .= "	  A.ACCESS_DATE ";
		$sql .= "	 ,A.ROUTE ";
		$sql .= "	 ,A.MESSAGE ";
		$sql .= "	 ,(DATEDIFF(CURRENT_TIMESTAMP(), A.ACCESS_DATE))			AS ACCESS_VAL_DAY ";
		$sql .= "	 ,TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP(), A.ACCESS_DATE))	AS ACCESS_VAL_TIME ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "ACCESS AS A ";
		$sql .= "WHERE ";
		$sql .= "	 A.USER_ID		= :user_id ";
		$sql .= "ORDER BY ";
		$sql .= "	  A.ACCESS_DATE DESC ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			$access_date = $row[0];				//ACCESS_DATE
			$route = $row[1];					//ROUTE
			$message = $row[2];					//MESSAGE
			$access_val_day = $row[3];			//ACCESS_VAL_DAY
			$access_val_time = $row[4];			//ACCESS_VAL_TIME
			echo "		<tr>";
			echo "			<th>$access_date</th>";
			echo "			<th>$ROUTE_ARRAY[$route]</th>";
			echo "			<th class='alignLeft'>$message</th>";
			echo "		</tr>";
		}
		echo "	</tbody>";
		echo "</table>";
	}catch(Exception $ex){
		$CMN->error = $ex->getMessage();
		$CMN->log->error("システムエラー:" . $CMN->error);
		throw $ex;
	}
}
?>