<?php
function DispScript(){

	try{
		echo "<script type='text/javascript'>";
		echo "	function infoview(informationCode){";
		echo "		$.ajax({";
		echo "			 type			: 'POST'";
		echo "			,url			: './ajax/editInformation.php'";
		echo "			,datatype		: 'html'";
		echo "			,data			: {";
		echo "				 Type				: 8";
		echo "				,InformationCode	: informationCode";
		echo "			 }";
		echo "			,cache		: false";
		echo "		}).done(function(data, textStatus, jqXHR){";
		echo "			$('#fadeLayer').show();";
		echo "			$('#modalForm').html(data);";
		echo "			$('#modalForm').show();";
		echo "		}).fail(function(data, textStatus, errorThrown){";
		echo "			alert('処理が異常終了しました');";
		echo "			rtn=false;";
		echo "		});";
		echo "	}";
		echo "	$(function(){";
		echo "		try{";
		echo "			$('#login').click(function(){";
		echo "				member_code = $('#member_code').val();";
		echo "				password = $('#password').val();";
		echo "				rtn = true;";
		echo "				if(member_code==''||password==''){";
		echo "					alert('未入力の項目があります');";
		echo "					rtn=false;";
		echo "				}";
		echo "				if(rtn==true){";
		echo "					$.ajax({";
		echo "						 type			: 'POST'";
		echo "						,url			: './ajax/chklogin.php'";
		echo "						,datatype		: 'html'";
		echo "						,data			: {";
		echo "							 MemberCode		: member_code";
		echo "							,PassWord		: password";
		echo "						 }";
		echo "						,cache		: false";
		echo "					}).done(function(data, textStatus, jqXHR){";
		echo "						if(data!=''){";
		echo "							alert(data);";
		echo "							rtn=false;";
		echo "						}else{";
		echo "							window.location = 'index.php?d=menu';";
		echo "						}";
		echo "					}).fail(function(jqXHR, textStatus, errorThrown){";
		echo "						alert(errorThrown.message);";
		echo "						console.log('jqXHR          : ' + jqXHR.status);";
		echo "						console.log('textStatus     : ' + textStatus);";
		echo "						console.log('errorThrown    : ' + errorThrown.message);";
		echo "						rtn=false;";
		echo "					});";
		echo "				}";
		echo "				return false;";
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
		echo "<table class='editable'>";
		echo "	<tbody>";
		echo "		<tr>";
		echo "			<th>ユーザーコード<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='text' name='member_code' id='member_code' value='' maxlength='10'>";
		echo "			</td>";
		echo "		</tr>";
		echo "		<tr>";
		echo "			<th>パスワード<span class='hissu'>*</span></th>";
		echo "			<td>";
		echo "				<input type='password' name='password' id='password' value='' maxlength='10'>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tbody>";
		echo "	<tfoot>";
		echo "		<tr>";
		echo "			<td>";
		echo "				<input type='button' name='login' id='login' value='ログイン'>";
		echo "			</td>";
		echo "		</tr>";
		echo "	</tfoot>";
		echo "</table>";
	}catch(Exception $ex){
		throw $ex;
	}

}
?>