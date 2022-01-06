<?php
function Header_Display($disp_name, $system_name){

    echo "<!DOCTYPE html>";
    echo "<html lang='ja'>";
    echo "<head>";
    echo "  <meta CONTENT='text/html; charset=UTF-8' HTTP-EQUIV='Content-Type'>";
    echo "  <meta http-equiv='Pragma' content='no-cache'>";
    echo "  <meta http-equiv='Cache-Control' content='no-cache'>";
    echo "  <meta http-equiv='expires' content='0'>";
    echo "  <meta name='viewport' content='width=device-width, initial-scale=1, minimum-scale=0.1, maximum-scale=1, user-scalable=no'>";
    echo "  <title>$disp_name - $system_name -</title>";
    echo "  <link rel='shortcut icon' href='./images/favicon.ico' />";
    echo "  <link rel='apple-touch-icon' href='./images/logo.png' />";
    echo "  <!--1.CSSの読み込み-->";
    echo "  <link rel='stylesheet' TYPE='text/css' href='./css/common.css?" . uniqid() . "'>";
    echo "  <!--2.プラグインの読み込み-->";
    echo "  <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>";
    echo "  <script type='text/javascript' src='./js/common.js'></script>";

}
function Body_Display($display_code){

	global $CMN;
	global $MY;
	global $DISPLAY_ARRAY;
    global $AUTHORITY;

	$display_name = $MY->display["display_name"];

	echo "  </head>";
    echo "  <body>";
    echo "      <ul id='menu'>";
    echo "          <li id='menuclose'><p class='togglebuttom'>▲</p></li>";
    echo "          <li>";
    if ($display_code == "login" ){
		echo "	<li>";
		echo "		<a href='./index.php'>ログイン</a>";
		echo "	</li>";
	}else{
		$rows = $DISPLAY_ARRAY;
		foreach ($rows as $key => $row){
            $exists_flg = strpos($row["display_authority"], $AUTHORITY[$MY->member["authority"]]);
            if($exists_flg !== false && $row["display_menu"] == "ON"){
                //DISPLAY_CODE（画面コード）
				$disp_code = $key;
				//DISPLAY_FILE（画面物理名）
				$disp_file = $row["display_file"];
				//DISPLAY_NAME（画面論理名）
				$disp_name = $row["display_name"];
				echo "	<li>";
				echo "		<a href='?d=$disp_code' title='$disp_file'>$disp_name</a>";
				echo "	</li>";
			}
		}
		echo "	<li>";
		echo "		<a href='./'>ログアウト</a>";
		echo "	</li>";
	}
    echo "      </ul>";
    echo "      <div id='title'>";
    echo "          <div id='toggle'><p class='togglebuttom'>▼</p></div>";
    echo "          <div id='titlename'>$display_name</div>";
    echo "          <div id='titlemembername'>";
    echo "              <span class='mobile'>" . $MY->member["company_name"] . "</span>" . $MY->member["name"];
    echo "          </div>";
    echo "      </div>";
    echo "      <div id='container'>";

}
function Footer_Display(){

    echo "      </div>";
    echo "      <div id='footer'>";
    echo "          <div class='copyright'>&copy;2021 " . SYSTEM_NAME . " " . VERSION . "</div>";
    echo "      </div>";
    echo "      <div id='fadeLayer'></div>";
    echo "      <div id='modalForm'></div>";
    echo "      <script type='text/javascript'>";
    echo "      $(function(){";
    echo "          $(window).on('load',function(){";
    echo "              $('#fadeLayer').hide();";
    echo "      	});";
    echo "          $('#fadeLayer').on('click',function(){";
    echo "              modalClose();";
    echo "      	});";
    echo "      });";
    echo "      function modalClose(){";
    echo "          $('#fadeLayer').hide();";
    echo "          $('#modalForm').hide();";
	echo "			$('#modalForm').html('');";
    echo "      }";
    echo "      function modalOpen(html){";
    echo "			$('#modalForm').html(html);";
    echo "			$('#fadeLayer').show();";
    echo "			$('#modalForm').show();";
    echo "      }";
    echo "      </script>";
    echo "  </body>";
    echo "</html>";

}
function GetDisplay($DispName){

	global $CMN;
	global $MY;
	global $DISPLAY_ARRAY;
	
	$result = false;
	try{
		$MY->display = $DISPLAY_ARRAY[$DispName];
        if($MY->display["display_name"]!=""){
            $result = true;
        }
	}catch(Exception $ex){
		$CMN->error =  $ex->getMessage();
	}
	return $result;

}
?>