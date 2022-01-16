<?php

require_once("./config.php");
require_once("./logger.php");

$log = Logger::getInstance();

//JSからのデータを受け取る
$mode = filter_input(INPUT_POST, 'mode');	        // $_POST['mode']とも書ける
$line_id = filter_input(INPUT_POST, 'line_id');	    // $_POST['line_id']とも書ける
$user_id = filter_input(INPUT_POST, 'user_id');	    // $_POST['user_id']とも書ける
$error_message = "";
$log->info('[ajax開始] mode:[' . $mode . '] user_id:[' . $user_id . '] line_id[' . $line_id . ']');
//DB接続
if($error_message == ""){
    $log->info('MESSAGE:DB接続[START]');
    try{
        $dbh = new PDO(DATABASE_URL, DATABASE_USER, DATABASE_PASS);
        // 静的プレースホルダを指定
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        // エラー発生時に例外を投げる
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $ex){
        $log->error("DB接続:" . $ex->getMessage());
        $error_message = "接続エラー";
    }
    $log->info('MESSAGE:DB接続[END]');
}
//ユーザー情報取得(LINEIDで取得できるかチェックする)
if($error_message == ""){
    $log->info('MESSAGE:ユーザー情報取得[START]');
    try{
        $SQL  = "SELECT ";
        $SQL .= "   USER_NAME ";
        $SQL .= "FROM ";
        $SQL .= "   " . DBPRE . "USER ";
        $SQL .= "WHERE ";
        $SQL .= "   LINE_ID		= :line_id ";

        $log->debug($SQL);
        $stmt = $dbh->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(':line_id', $line_id, PDO::PARAM_STR);
        $stmt->execute();
        //データ取得(LINEIDで取得したユーザー情報を格納する)
        if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
            $user_name = $row[0];
            $log->info('データあり user_name[' . $user_name . ']');
        }
    }catch(PDOException $ex){
        $log->error("ユーザー情報取得:" . $ex->getMessage());
        $error_message = "ユーザー情報取得エラー";
    }
    $log->info('MESSAGE:ユーザー情報取得[END]');
}
// 更新処理の時はすでにユーザーIDがあるかチェックする
if($error_message == "" && $mode == "set"){
    if ($user_id==""){
        $error_message = "ユーザーIDが入力されていません";
    }else{
        $log->info('MESSAGE:ユーザー情報取得[START]');
        try{
            $SQL  = "SELECT ";
            $SQL .= "   LINE_ID, ";
            $SQL .= "   USER_NAME ";
            $SQL .= "FROM ";
            $SQL .= "   " . DBPRE . "USER ";
            $SQL .= "WHERE ";
            $SQL .= "   USER_ID		= :user_id ";

            $log->debug($SQL);
            $stmt = $dbh->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->execute();
        }catch(PDOException $ex){
            $log->error("ユーザー情報取得:" . $ex->getMessage());
            $error_message = "ユーザー情報取得エラー";
        }
        $log->info('MESSAGE:ユーザー情報取得[END]');
        //データ取得
        if($error_message == ""){
            $log->info('MESSAGE:データ取得[START]');
            if($stmt->rowCount()==0){
                $error_message = "ユーザーIDが登録されていません";
            }
            $log->info('MESSAGE:データ取得[END]');
        }
    }
}
//アクション
if($error_message == ""){
    if($mode == "get"){
        if ($user_name==""){
            $status = "NA";
        }
    }elseif($mode == "set"){
        $log->info('MESSAGE:ユーザー情報更新[START]');
        //トランザクション処理を開始
        $dbh->beginTransaction();
        try {
            $log->info('USER_ID:' . $user_id);
            $log->info('LINE_ID:' . $line_id);
            $SQL  = "UPDATE " . DBPRE . "USER ";
            $SQL .= "SET ";
            $SQL .= "    LAST_ACCESS = NOW() ";
            $SQL .= "   ,LINE_ID     = :line_id ";
            $SQL .= "WHERE ";
            $SQL .= "   USER_ID     = :user_id ";

            $log->debug($SQL);
            $stmt = $dbh->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->bindParam(':line_id', $line_id, PDO::PARAM_STR);
            $stmt->execute();

            //コミット
            $dbh->commit();
        }catch(Exception $ex){
            //ロールバック
            $dbh->rollback();
            $log->error("ステータス更新:" . $ex->getMessage());
            $error_message = "登録に失敗しました";
        }
        $log->info('MESSAGE:ユーザー情報更新[END]');
    }else{
        $error_message = "予期しないエラーが発生しました";
    }
}
$list = array(
    "error" => $error_message,
    "status" => $status
);

//JSONデータを出力
header("Content-Type: application/json; charset=UTF-8"); //ヘッダー情報の明記。必須。
echo json_encode($list);
exit; //処理の終了
?>