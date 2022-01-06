<?php
function DispScript(){
	
	try{
	
		echo "<script type='text/javascript'>";
		echo "	$(function(){";
		echo "		try{";
		//登録処理
		echo "			$('.edit').click(function(){";
		echo "				target=$(this).attr('alt');";
		echo "				window.location = 'index.php?d=company_edit&c='+target;";
		echo "			});";
		//削除処理
		echo "			$('.delete').click(function(){";
		echo "				companyCode=$(this).attr('alt');";
		echo "				if(confirm('削除しますか？')){";
		echo "					$.ajax({";
		echo "						 type			: 'POST'";
		echo "						,url			: './ajax/editCompany.php'";
		echo "						,datatype		: 'html'";
		echo "						,data			: {";
		echo "							 Type			: 9";
		echo "							,CompanyCode	: companyCode";
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
		//会社情報一覧
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
		$sql .= "FROM ";
		$sql .= "	" . DBPRE . "M_COMPANY AS C ";
		$sql .= "ORDER BY ";
		$sql .= "	C.COMPANY_CODE ";
		$sql .= "";
		$stmt = $CMN->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->execute();
		echo "<input type='button' class='edit' value='新規' alt='d=company_edit'>";
		echo "<table class='listable'>";
		echo "	<thead>";
		echo "		<tr>";
		echo "			<th>処理</th>";
		echo "			<th>地区名</th>";
		echo "			<th class='mobile'>住所</th>";
		echo "			<th>詳細</th>";
		echo "		</tr>";
		echo "	</thead>";
		echo "	<tbody>";
		while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {			
			$company_code = $row[0];	//COMPANY_CODE（会社コード）
			$company_name = $row[1];	//COMPANY_NAME（会社名）
			$company_post = $row[2];	//COMPANY_POST（会社郵便番号）
			$company_ken = $row[3];		//COMPANY_KEN（会社都道府県）
			$company_shi = $row[4];		//COMPANY_SHI（会社市区町村）
			$company_add1 = $row[5];	//COMPANY_ADDRESS１（会社住所１）
			$company_add2 = $row[6];	//COMPANY_ADDRESS２（会社住所２）
			$company_tel = $row[7];		//COMPANY_TEL（会社電話番号）
			$company_web = $row[8];		//COMPANY_WEB（会社ホームページ）
			$company_mail = $row[9];	//COMPANY_MAIL（会社メールアドレス）
			//表示用に住所をつなげる
			$address = $company_post . " " . $company_ken . $company_shi . $company_add1 . $company_add2;

			echo "		<tr>";
			echo "			<td class='alignCenter'>";
			echo "				<img class='edit-button' alt='$company_code' src='./css/img/menu.png'>";
			echo "				<ul class='edit-menu' id='edit-menu".$company_code."' alt='$company_code'>";
			echo "					<li class='edit' alt='".$company_code."'>修正</li>";
			echo "					<li class='delete' alt='$company_code'>削除</li>";
			echo "				</ul>";
			echo "			</td>";
			echo "			<th>$company_name</th>";
			echo "			<td class='mobile'>$address</td>";
			echo "			<th>";
			echo "				<table class='listable-table'>";
			echo "					<tr>";
			//電話番号が存在したら表示する
			echo "						<td>";
			if($company_tel!=""){
				echo "							<a class='listable-icon listable-icon-tel' title='電話' href='tel:$company_tel'></a>";
			}
			echo "						</td>";
			//ホームページが存在したら表示する
			echo "						<td>";
			if($company_web!=""){
				echo "							<a class='listable-icon listable-icon-web' title='ホームページ' href='$company_web' target='blank'></a>";
			}
			echo "						</td>";
			//メールアドレスが存在したら表示する
			echo "						<td>";
			if($company_mail!=""){
				echo "							<a class='listable-icon listable-icon-mail' title='メール' href='mailto:$company_mail'></a>";
			}
			echo "						</td>";
			echo "					</tr>";
			echo "				</table>";
			echo "			</th>";
			echo "		</tr>";
		}
		echo "	</tbody>";
		echo "</table>";

	}catch(Exception $ex){
		throw $ex;
	}

}
?>