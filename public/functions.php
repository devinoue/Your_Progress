<?php

/**
 * 様々な用途に使う関数ファイル。
 * @author Masaharu Inoue <pasteur1822@gmail.com>
 */


/**
 * ユーザー定義のエラーハンドラー
 * 使用例) trigger_error("Incorrect input vector, array of values expected", E_USER_ERROR);
 * @access  Public
 * @param  String $err_level エラーレベル。
 *                           E_USER_NOTICE(注意)
 *                           E_USER_WARNING(警告)
 *                           E_USER_ERROR(処理が停止します)
 * @param  String $err_msg   表示するエラーメッセージ
 * @param  String $err_file  エラーが発生したファイル
 * @param  String $err_line  エラーが発生した行
 * @return none            
 */
function userErrorHandler($err_level,$err_msg, $err_file=null, $err_line=null)
{

	if (!(error_reporting() & $err_level)) {
        return;
    }

	if (headers_sent() === false) {
		header("Content-Type: text/html; charset: UTF-8;");
	}

	$main_err_msg=[];
	$main_err_msg[] = "<h1>Error Level: $err_level</h1>\n";
	$message[] = $err_msg . "<br>";
	if ($err_file !== null) {
		$main_err_msg[] = "Error file : " . $err_file;
	}
	if ($err_line !== null) {
		$main_err_msg[] = "Error line : " . $err_line;
	}
	if (DEBUG_MODE) {
		print join("<br>", $main_err_msg) . "<br>";
	} else {
		//Error発生箇所を担当者にメールする
		if (REPORT_ERROR) {
			mb_send_mail(ERROR_EMAIL_ADDRESS , THIS_PROJECT_NAME . "エラーについて", join("\n",$main_err_msg));
		}
		exit("大変申し訳ありませんが、システムエラーのため、処理を中断しました。");
	}

}

/**
 * HTMLタグの無効化。
 * @param  String $str サニタイズしたい文字列
 * @return String      サニタイズされた文字列
 */
function h($str)
{
	return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}