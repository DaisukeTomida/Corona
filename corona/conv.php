<?php
function DispScript(){

}
function DispBody(){

	global $CMN;

	if($_GET['logic'] != ""){
		$logic	= $_GET['logic'];
		$chg		= $_GET['chg'];
		$t = ConvSecret($logic, $chg);
	}
	echo "	<form method='get'>";
	echo "		<input type='hidden' name='d' id='d' value='conv' >";
	echo "		<div>";
	echo "			<input type='text' name='logic' value='$logic'>";
	echo "			<label>";
	if($chg=="1"){
		echo "			<input type='radio' name='chg' value='1' checked='checked'>暗号化";
	}else{
		echo "			<input type='radio' name='chg' value='1'>暗号化";
	}
	echo "			</label>";
	echo "			<label>";
	if($chg=="9"){
		echo "			<input type='radio' name='chg' value='9' checked='checked'>複合化";
	}else{
		echo "			<input type='radio' name='chg' value='9'>複合化";
	}
	echo "			</label>";
	echo "			<input type='submit' value='実行'>";
	echo "			<input type='text' name='answer' value='$t'>";
	echo "		</div>";
	echo "	</form>";
}
?>