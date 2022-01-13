<?php
    require_once("./config.php");
    header( 'Expires: Thu, 01 Jan 1970 00:00:00 GMT' );
    header( 'Last-Modified: '.gmdate( 'D, d M Y H:i:s' ).' GMT' );
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <title>ユーザー登録</title>
        <style>
        *{
            margin: 0;
            padding: 0;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            font-size:120%;
        }
        input, textarea {
            -webkit-user-select : auto;
            -moz-user-select: auto;
            -ms-user-select: auto;
            user-select: auto;
        }
        :focus {
            outline: none;
        }
        a{
            text-decoration: none;            
        }
        #container{
            width:80%;
            margin:0 auto;
        }
        #title{
            position:fixed;
            height:10%;
            left:0;
            right:0;
            top:0;
            text-align:center;
        }
        #user_id{
            position:fixed;
            height:10%;
            width:80%;
            left:10%;
            right:10%;
            top:20%;
        }
        #message{
            position:fixed;
            bottom:20%;
            left:10%;
            right:10%;
            width:80%;
            top:40%;
            height:30%;
            color:#f00;
        }
        #submit{
            position: fixed;
            bottom: 10%;
            height:10%;
            width:80%;
            left:10%;
            right:10%;
        }
        #loading{
            position: fixed;
            height:100%;
            width:100%;
            top:0;
            bottom: 0%;
            left:0%;
            right:0%;
            z-index:999;
            background-color:#fff;
            text-align:center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        </style>
        <!-- jQuery読み込み -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
        <script>
        $(function() {
            // LIFFの初期化を行う
            $('#log').append('START<br>');
            liff.init({
                // 自分のLIFF ID（URLから『https://liff.line.me/』を除いた文字列）を入力する
                liffId: "<?= LIFF_ID ?>"
            }).then(() => { // 初期化完了. 以降はLIFF SDKの各種メソッドを利用できる
                $('#log').append('GET<br>');
                // 利用者のLINEアカウントのプロフィール名を取得
                liff.getProfile().then(function(profile) {
                    // ユーザーID
                    const line_id = profile.userId;
                    // プロフィール名
                    const name = profile.displayName;
                    // HTMLに挿入
                    $("#line_id").val(line_id);
                    $('#log').append('liff.getProfile()=' + JSON.stringify(profile) + '<br>');
                    get_profile(line_id);
                    $('#log').append('END<br>');
                }).catch(function(error) {
                    $('#log').append('ERROR<br>');
                });
            })
            function get_profile(line_id){
                $('#log').append('get_profile START<br>');
                try {
                    $.ajax({
                        type			: 'POST'
                        ,url			: 'ajax_line.php'
                        ,dataType       : 'json'
                        ,data			: {
                             mode   		: 'get'
                            ,line_id		: line_id
                         }
                        ,cache		    : false
                    }).done(function(data, textStatus, jqXHR){
                        //エラーメッセージが存在しない場合は再描画
                        if(data['error']==""){
                            if(data['status']=="NA"){
                                $("#loading").hide();
                            }else{
                                $("#load_error").text("すでに登録されています");
                                setTimeout(function(){
                                    liff.closeWindow();
                                },2000);
                            }
                        }else{
                            $("#load_error").text(data['error']);
                        }
                    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
                        $('#log').append('ERROR<br>');
                        $("#load_error").text("データを取得できません");
                    });
                }catch(e){
                    $('#log').append('ERROR<br>');
                    $("#load_error").text(e.message);
                }
                $('#log').append('get_profile END<br>');
            }
            function set_profile(){
                var user_id = $("#user_id").val();
                var line_id = $("#line_id").val();
                $('#log').append('set_profile START<br>');
                try {
                    $.ajax({
                        type			: 'POST'
                        ,url			: 'ajax_line.php'
                        ,dataType       : 'json'
                        ,data			: {
                             mode   		: 'set'
                            ,user_id		: user_id
                            ,line_id		: line_id
                         }
                        ,cache		    : false
                    }).done(function(data, textStatus, jqXHR){
                        //エラーメッセージが存在しない場合は再描画
                        if(data['error']==""){
                            $("#message").text("登録しました");
                            setTimeout(function(){
                                liff.closeWindow();
                            },2000);
                        }else{
                            $("#message").text(data['error']);
                        }
                    }).fail(function(XMLHttpRequest, textStatus, errorThrown){
                        $("#message").text("データを取得できません");
                    });
                }catch(e){
                    $("#message").text(e.message);
                }
                $('#log').append('set_profile END<br>');
            }
            //登録ボタン押下処理
            $("body").on({
                "click" : function(){
                    set_profile();
                }
            }, "#submit")
        });
        </script>
    </head>
    <body>
        <div id="container">
            <h1 id="title">連絡を受けたユーザーIDを入力してください。</h1>
            <input type="text" id="user_id">
            <input type="hidden" id="line_id">
            <input type="button" id="submit" value="登録">
        </div>
        <div id="message"></div>
        <div id="log" style="display:none;width:96%;height:100px;"></div>
        <div id="loading"><p id="load_error">now loading...</p></div>
    </body>
</html>
<!-- liff.closeWindow() -->
