<?php

require_once("./config.php");
require_once("./logger.php");

$user_id = "";
$user_name = "";
$company_code = "";
$message_config = [
    "平熱"  => "熱はありません",
    "微熱"  => "多少熱がある程度です",
    "高熱"  => "熱が高いです",
    "OK"    => "問題ないです",
    "NG"    => "体調に変化がありますが、今のところは大丈夫です",
    "HELP"  => "体調に変化があったので早急に連絡ください",
];

$log = Logger::getInstance();
// $log->error('error log.');
// $log->warn('warn log.');
// $log->info('info log.');
// $log->debug('debug log.');

//ユーザーからのメッセージ取得
$j_string = file_get_contents('php://input');
$j_object = json_decode($j_string);
 
$log->debug($j_string);
//取得データ
$replyToken = $j_object->{"events"}[0]->{"replyToken"};              //返信用トークン
$line_id = $j_object->{"events"}[0]->{"source"}->{"userId"};         //LINE_ID
$message_type = $j_object->{"events"}[0]->{"message"}->{"type"};     //メッセージタイプ
$message_text = $j_object->{"events"}[0]->{"message"}->{"text"};     //メッセージ内容
 
//メッセージタイプが「text」以外のときは何も返さず終了
if($message_type != "text") exit;
$log->info('LINE_ID:' . $line_id);
$log->info('MESSAGE:' . $message_text);
//DB接続
$log->info('MESSAGE:DB接続[START]');
try{
    $dbh = new PDO(DATABASE_URL, DATABASE_USER, DATABASE_PASS);
    // 静的プレースホルダを指定
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // エラー発生時に例外を投げる
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $ex){
    $log->error("DB接続:" . $ex->getMessage());
    exit;
}
$log->info('MESSAGE:DB接続[END]');
//ユーザー情報取得
$log->info('MESSAGE:ユーザー情報取得[START]');
try{
    $SQL  = "SELECT ";
    $SQL .= "    USER_ID ";
    $SQL .= "   ,USER_NAME ";
    $SQL .= "   ,USER_STATUS ";
    $SQL .= "   ,COMPANY_CODE ";
    $SQL .= "FROM ";
    $SQL .= "   " . DBPRE . "USER ";
    $SQL .= "WHERE ";
    $SQL .= "   LINE_ID		= :line_id ";

    $log->debug($SQL);
    $stmt = $dbh->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
    $stmt->bindParam(':line_id', $line_id, PDO::PARAM_STR);
    $stmt->execute();
}catch(PDOException $ex){
    $log->error("ユーザー情報取得:" . $ex->getMessage());
    exit;
}
$log->info('MESSAGE:ユーザー情報取得[END]');
$log->info('MESSAGE:データ取得[START]');
if($stmt->rowCount()>=0){
    $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
    $user_id = $row[0];
    $user_name = $row[1];
    $user_status = $row[2];
    $company_code = $row[3];
    $log->info('user_id:[' . $user_id . '] user_name[' . $user_name . ']');
}
$log->info('MESSAGE:データ取得[END]');

//会社コードが存在している場合は情報を取得する
if ($company_code != ''){
    $log->info('MESSAGE:会社情報取得[START]');
    try{
        $SQL  = "SELECT ";
        $SQL .= "    COMPANY_NAME ";
        $SQL .= "   ,COMPANY_POST ";
        $SQL .= "   ,COMPANY_KEN ";
        $SQL .= "   ,COMPANY_SHI ";
        $SQL .= "   ,COMPANY_ADDRESS1 ";
        $SQL .= "   ,COMPANY_ADDRESS2 ";
        $SQL .= "   ,COMPANY_TEL ";
        $SQL .= "   ,COMPANY_WEB ";
        $SQL .= "FROM ";
        $SQL .= "   " . DBPRE . "M_COMPANY ";
        $SQL .= "WHERE ";
        $SQL .= "   COMPANY_CODE		= :company_code ";
    
        $log->debug($SQL);
        $stmt = $dbh->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(':company_code', $company_code, PDO::PARAM_STR);
        $stmt->execute();
        $log->info('MESSAGE:データ取得[START]');
        if($stmt->rowCount()>=0){
            $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
            $company_name = $row[0];
            $company_post = $row[1];
            $company_ken = $row[2];
            $company_shi = $row[3];
            $company_address1 = $row[4];
            $company_address2 = $row[5];
            $company_tel = $row[6];
            $company_web = $row[7];
            $log->info('company_name:[' . $company_name . ']');
        }
    }catch(PDOException $ex){
        $log->error("会社情報取得:" . $ex->getMessage());
        exit;
    }
    $log->info('MESSAGE:会社情報取得[END]');
}

if ($user_id==''){
    //ユーザー登録未完了
    $response_format_text = [
        [
            "type" => "text",
            "text" => "ユーザーの登録をしてください"
        ]
    ];
} else {
    //ユーザー登録完了
    if($user_status =='CLEAR'){
        //完了している場合
        $return_message_text  = "[住所] 〒" . $company_post . "\n";
        $return_message_text .= $company_ken . $company_shi . $company_address1 . $company_address2 . "\n";
        $return_message_text .= "[連絡先] " . $company_tel . "\n";
        if($company_web != ''){
            $return_message_text .= "[ホームーページ]\n";
            $return_message_text .= $company_web . "\n";
        }
        $url = "https://www.google.com/maps?q=" . $company_ken . $company_shi . $company_address1 . $company_address2;
        // レスポンスフォーマット
        $response_format_text = [
            [
                "type" => "text",
                "text" => "このチャンネルでの連絡は終了しています。\n別途ご連絡ください"
            ],
            [
                "type" => "template",
                "altText" => "連絡先情報",
                "template" =>  [
                    "type" => "buttons",
                    "title" => $company_name,
                    "text" => $return_message_text,
                    "actions" => [
                            [
                                "type" => "uri",
                                "label" => "位置情報",
                                "uri" => $url
                            ]
                        ]
                ]
            ]
        ];
    }else{
        if ((strpos($message_text,'体温報告')) !== false) { 
            //体温測定
            $response_format_text = [
                [
                    "type" => "template",
                    "altText" => "現在の体温を教えてください",
                    "template" =>  [
                        "type" => "buttons",
                        "title" => "体温調査",
                        "text" => "現在の体温を教えてください",
                        "actions" => [
                                [
                                    "type" => "message",
                                    "label" => "平熱",
                                    "text" => "平熱"
                                ],
                                [
                                    "type" => "message",
                                    "label" => "微熱",
                                    "text" => "微熱"
                                ],
                                [
                                    "type" => "message",
                                    "label" => "高熱",
                                    "text" => "高熱"
                                ],
                            ]
                    ]
                ]
            ];
        } elseif ((strpos($message_text,'体調報告')) !== false) { 
            //体調返答
            $response_format_text = [
                [
                    "type" => "template",
                    "altText" => "現在の体調を教えてください",
                    "template" =>  [
                        "type" => "buttons",
                        "title" => "体調調査",
                        "text" => "現在の体調を教えてください",
                        "actions" => [
                                [
                                    "type" => "message",
                                    "label" => "無症状",
                                    "text" => "OK"
                                ],
                                [
                                    "type" => "message",
                                    "label" => "熱があるまたは息苦しい",
                                    "text" => "NG"
                                ],
                                [
                                    "type" => "message",
                                    "label" => "熱があり息苦しい",
                                    "text" => "HELP"
                                ],
                            ]
                    ]
                ]
            ];
        }else{
            switch($message_text){
                case "平熱":
                case "微熱":
                case "高熱":
                    //体温調査
                case "OK":
                case "NG":
                case "HELP":
                    //体調報告
                    if(update_status($dbh, $log, $user_id, $message_config[$message_text])==false){
                        exit;
                    }
                    $return_message_text = "連絡しました";
                    break;
                default: 
                    $return_message_text = "申し訳ありません。\r\n「" . $message_text . "」については返答しかねます";
            };
            // レスポンスフォーマット
            $response_format_text = [
                [
                    "type" => "text",
                    "text" => $return_message_text
                ]
            ];
        }
    }
}
//ポストデータ
$post_data = [
    "replyToken" => $replyToken,
    "messages" => $response_format_text
];
 
//返信実行
sending_messages(ACCESSTOKEN, $post_data);

?>
<?php
//ステータス更新
function update_status($dbh, $log, $user_id, $message_text){
    $log->info('MESSAGE:ステータス更新[START]');
    $rtn=false;
    //トランザクション処理を開始
    $dbh->beginTransaction();
    try {
        $SQL  = "INSERT INTO " . DBPRE . "ACCESS( ";
		$SQL .= "   USER_ID, ";
		$SQL .= "   ACCESS_DATE, ";
		$SQL .= "   ROUTE, ";
		$SQL .= "   MESSAGE ";
		$SQL .= ")VALUES( ";
		$SQL .= "   :user_id, ";
		$SQL .= "   NOW(), ";
		$SQL .= "   'LINE', ";
		$SQL .= "   :message ";
		$SQL .= ") ";

        $log->debug($SQL);
        $stmt = $dbh->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message_text, PDO::PARAM_STR);
        $stmt->execute();

        $SQL  = "UPDATE " . DBPRE . "USER ";
		$SQL .= "SET ";
		$SQL .= "   LAST_ACCESS = NOW() ";
		$SQL .= "WHERE ";
		$SQL .= "   USER_ID     = :user_id ";

        $log->debug($SQL);
        $stmt = $dbh->prepare($SQL, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();

        //コミット
        $dbh->commit();
        $rtn = true;
    }catch(Exception $ex){
        //ロールバック
        $dbh->rollback();
        $log->error("ステータス更新:" . $ex->getMessage());
    }
    $log->info('MESSAGE:ステータス更新[END]');
    return $rtn;
}

//メッセージの送信
function sending_messages($accessToken, $post_data){

    //curl実行
    $ch = curl_init("https://api.line.me/v2/bot/message/reply");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer ' . $accessToken
    ));
    $result = curl_exec($ch);
    curl_close($ch);
}

?>