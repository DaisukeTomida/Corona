<?php
$AUTHORITY		= array("", "[sysadmin]", "[admin]", "[puser]", "[user]");
$SEX_ARRAY      = [
    ""          => "未設定",
    "男"        => "男",
    "女"        => "女",
    "その他"    => "その他",
    "不明"      => "不明",
];
$STATUS_ARRAY   = [
    "ENTRY"         => "未確認",
    "STAY"          => "自宅療養中",
    "INBED"         => "入院",
    "CLEAR"         => "完了",
];
$ROUTE_ARRAY    = [
    "LINE"          => "LINE通知",
    "STATUS"        => "状態変更",
    "MESSAGE"       => "連絡済",
];
$DISPLAY_ARRAY  = [
    "login"         =>  [
                            "display_name"           => "ログイン",
                            "display_file"           => "login.php",
                            "display_authority"      => "[sysadmin][admin][puser][user]",
                            "display_befor"          => "",
                            "display_menu"           => "OFF",
                        ],
    "menu"          =>  [
                            "display_name"           => "メニュー",
                            "display_file"           => "menu.php",
                            "display_authority"      => "[sysadmin][admin][puser][user]",
                            "display_befor"          => "",
                            "display_menu"           => "ON",
                        ],
    "contact"       =>  [
                            "display_name"           => "連絡情報",
                            "display_file"           => "contact.php",
                            "display_authority"      => "[sysadmin][admin][puser][user]",
                            "display_befor"          => "",
                            "display_menu"           => "ON",
                        ],
    "contact_edit"  =>  [
                            "display_name"           => "連絡修正",
                            "display_file"           => "contact_edit.php",
                            "display_authority"      => "[sysadmin][admin][puser][user]",
                            "display_befor"          => "contact",
                            "display_menu"           => "OFF",
                        ],
    "user"          =>  [
                            "display_name"           => "利用者情報",
                            "display_file"           => "user.php",
                            "display_authority"      => "[sysadmin][admin][puser]",
                            "display_befor"          => "",
                            "display_menu"           => "ON",
                        ],
    "user_edit"  =>  [
                            "display_name"           => "利用者修正",
                            "display_file"           => "user_edit.php",
                            "display_authority"      => "[sysadmin][admin][puser]",
                            "display_befor"          => "user",
                            "display_menu"           => "OFF",
                        ],
    "company"       =>  [
                            "display_name"           => "地区情報",
                            "display_file"           => "company.php",
                            "display_authority"      => "[sysadmin]",
                            "display_befor"          => "",
                            "display_menu"           => "ON",
                        ],
    "company_edit"  =>  [
                            "display_name"           => "地区修正",
                            "display_file"           => "company_edit.php",
                            "display_authority"      => "[sysadmin]",
                            "display_befor"          => "company",
                            "display_menu"           => "OFF",
                        ],
    "member"       =>  [
                            "display_name"           => "作業者情報",
                            "display_file"           => "member.php",
                            "display_authority"      => "[sysadmin][admin]",
                            "display_befor"          => "",
                            "display_menu"           => "ON",
                        ],
    "member_edit"  =>  [
                            "display_name"           => "作業者修正",
                            "display_file"           => "member_edit.php",
                            "display_authority"      => "[sysadmin][admin]",
                            "display_befor"          => "member",
                            "display_menu"           => "OFF",
                        ],
    "my_member"       =>  [
                            "display_name"           => "個人情報",
                            "display_file"           => "my_member.php",
                            "display_authority"      => "[sysadmin][admin][puser][user]",
                            "display_befor"          => "",
                            "display_menu"           => "ON",
                        ],
    "404"           =>  [
                            "display_name"           => "404エラー",
                            "display_file"           => "404.php",
                            "display_authority"      => "[sysadmin][admin][puser][user]",
                            "display_befor"          => "",
                            "display_menu"           => "OFF",
                        ],
    "conv"           =>  [
                            "display_name"           => "暗号化変換",
                            "display_file"           => "conv.php",
                            "display_authority"      => "[sysadmin]",
                            "display_befor"          => "",
                            "display_menu"           => "ON",
                        ],
];



?>
