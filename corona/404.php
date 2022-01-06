<?php
function DispScript(){

	try{
		echo "<script type='text/javascript'>";
		echo "	$(function(){";
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
		echo "<h1>ページが存在しません</h1>";
	}catch(Exception $ex){
		throw $ex;
	}

}
?>