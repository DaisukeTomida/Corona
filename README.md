# Corona

自宅療養連絡ツール


#更新履歴

2022/01/13　LINE内LIFFのユーザー登録時、i-phoneの端末でユーザーIDが入力できないのを修正

# フォルダ構成

┣ corona　Web管理用

┣ line　LINEbot用

┣ MySQL_CreateTable.sql　MySQLテーブル作成用SQL

┗ MySQL_InsertData.sql　MySQLデータ作成用SQL


# 変更箇所


corona/line_connect.php

  データベース接続情報
  
  暗号化キー

line/config.php

  データベース接続情報

  LINE Bot用情報

# 初期設定

１．データベース等の接続情報を設定します。

２．LINE Bot用の情報を設定します。

３．MySQL_CreateTable.sqlを使用してテーブルを作成します。

４. MySQL_InsertData.sqlを使用してデータを追加します。

５．初期設定時のユーザーはuser:1 password:testで設定されています。

６．ユーザー情報はすぐに変更してください。

# その他

データに保存する際にいくつかの項目は暗号化されます。

corona/line_connect.phpに暗号化キーを入れる箇所がありますが、キーの変更をしておくとよいです。

ただし、すでに入っているデータも変更しないといけないのでcorona/conv.phpを利用して変更してください。

