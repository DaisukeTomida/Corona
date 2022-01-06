<?php
function DispScript(){
	
	try{
		echo "<script type='text/javascript'>";
		echo "	$(function(){";
		echo "		try{";
		//登録処理
		echo "			$('.edit').click(function(){";
		echo "				target=$(this).attr('alt');";
		echo "				window.location = 'index.php?d=member_edit&c='+target;";
		echo "			});";
		//切替処理
		echo "			$('.change').click(function(){";
		echo "				memberCode=$(this).attr('alt');";
		echo "				if(confirm('切替しますか？')){";
		echo "					$.ajax({";
		echo "						 type			: 'POST'";
		echo "						,url			: './ajax/editMember.php'";
		echo "						,datatype		: 'html'";
		echo "						,data			: {";
		echo "							 Type			: 3";
		echo "							,MemberCode		: memberCode";
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
		//削除処理
		echo "			$('.delete').click(function(){";
		echo "				memberCode=$(this).attr('alt');";
		echo "				if(confirm('削除しますか？')){";
		echo "					$.ajax({";
		echo "						 type			: 'POST'";
		echo "						,url			: './ajax/editMember.php'";
		echo "						,datatype		: 'html'";
		echo "						,data			: {";
		echo "							 Type			: 9";
		echo "							,MemberCode		: memberCode";
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
		//ユーザー一覧情報の取得
		$sql  = "SELECT ";
		$sql .= "	  M.MEMBER_CODE ";
		$sql .= "	 ,M.MEMBER_NAME ";
		$sql .= "	 ,M.MEMBER_MAIL ";
		$sql .= "	 ,M.MEMBER_ENABLE ";
		$sql .= "	 ,M.MEMBER_AUTHORITY ";
		$sql .= "	 ,C.COMPANY_NAME ";
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "M_MEMBER AS M ";
		$sql .= "		LEFT JOIN " . DBPRE . "M_COMPANY AS C ";
		$sql .= "			ON	M.COMPANY_CODE = C.COMPANY_CODE ";
		$sql .= "ORDER BY ";
		$sql .= "	M.MEMBER_CODE ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->execute();
		echo "<input type='button' class='edit' value='新規' alt=''>";
		echo "<table class='listable'>";
		echo "	<thead>";
		echo "		<tr>";
		echo "			<th>処理</th>";
		echo "			<th class='mobile'>コード</th>";
		echo "			<th>名前</th>";
		echo "			<th class='mobile'>地区名</th>";
		echo "			<th class='mobile'>権限</th>";
		echo "			<th class='mobile'>詳細</th>";
		echo "		</tr>";
		echo "	</thead>";
		echo "	<tbody>";
		$cnt=1;
		while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			$member_code = $row[0];					//MEMBER_CODE（ユーザーコード）
			$member_name = $row[1];					//MEMBER_NAME（ユーザー名）
			$member_mail = $row[2];					//MEMBER_MAIL（ユーザーメールアドレス）
			$member_enable = intval($row[3]);		//MEMBER_ENABLE （ユーザー有効フラグ）
			$member_auth = $row[4];					//MEMBER_AUTHORITY（ユーザー権限）
			$company_name = $row[5];				//COMPANY_NAME（会社名）
			if($member_enable==1){
				$member_enable_name="無効にする";
				$member_enable_style="";
			}else{
				$member_enable_name="有効にする";
				$member_enable_style="style='background:gray;'";
			}
			echo "		<tr $member_enable_style>";
			echo "			<th class='alignCenter'>";
			echo "				<img class='edit-button' alt='$cnt' src='./css/img/menu.png'>";
			echo "				<ul class='edit-menu' id='edit-menu".$cnt."' alt='$cnt'>";
			echo "					<li class='change' alt='$member_code'>$member_enable_name</li>";
			echo "					<li class='edit' alt='$member_code'>修正</li>";
			echo "					<li class='delete' alt='$member_code'>削除</li>";
			echo "				</ul>";
			echo "			</th>";
			echo "			<td class='mobile'>$member_code</td>";
			echo "			<th>$member_name</th>";
			echo "			<td class='mobile'>$company_name</td>";
			echo "			<td class='mobile'>$AUTHORITY[$member_auth]</td>";
			echo "			<th class='mobile'>";
			echo "				<table class='listable-table'>";
			echo "					<tr>";
			//メールアドレスが存在したら表示する
			echo "						<td>";
			if($member_mail!=""){
				echo "							<a class='listable-icon listable-icon-mail' title='メール' href='mailto:$member_mail'></a>";
			}
			echo "						</td>";
			echo "					</tr>";
			echo "				</table>";
			echo "			</th>";
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