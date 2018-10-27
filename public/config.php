<?php


ini_set('display_errors',1);

//デバッグモード
//リリースの際はゼロ
define('DEBUG_MODE','1');

//プロジェクトの名前
define('THIS_PROJECT_NAME','Your Progress | 貴方の進捗率');

//MySQL
if (DEBUG_MODE == 1) {
	define ('DSN','mysql:host=localhost;dbname=todos;charset=utf8');
	define('DB_USERNAME','dbuser');
	define('DB_PASSWORD','abcd');
} else {
	define ('DSN','mysql:host=localhost;dbname=todos;charset=utf8');
	define('DB_USERNAME','dbuser');
	define('DB_PASSWORD','abcd');

}

//エラー発生時に、エラー箇所についてメールを送信するか(0=しない。1=する)
define('REPORT_ERROR',0);
//エラー発生時に報告するメールアドレス
define('ERROR_EMAIL_ADDRESS','');





