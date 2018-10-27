<?php

/**
 * データベース接続クラス
 * PDO拡張クラスを使用して安全なデータベースの接続と管理をします。
 * 特に配列を利用することで、長大なSQL文の作成と、静的プレースホルダを用いた安全な接続が可能です。
 * @author Masaharu Inoue <pasteur1822@gmail.com>
 * @license MIT 
 */
namespace Ronbun;

class DB
{
	public $db;

/**
 * @constructor
 *
 */
	public function __construct()
	{
		try{
			$this->db = new \PDO(DSN,DB_USERNAME,DB_PASSWORD);
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
		  echo $e->getMessage();
		  exit;
		}
	}


	/**
	 * 指定された配列から、SQL文を生成する。
	 * 主に長大なカラム数を持つテーブルに使用する。
	 * SQL文だけの発行もでき、実行せずに後からUNION句などで繋ぎたいときに使える。
	 * @param  Array $data    配下に以下のkeyを持つ２次元配列
	 * 												column=カラム
	 * 												table=テーブル
	 * 												where=条件
	 * @param  String $action 主なSQL文を振り分ける
	 * 												ins = INSERT
	 * 												upd = UPDATE
	 * 												rep = REPLACE
	 * 												del = DELETE
	 * @param  Boolean $no_execute					false:SQLをそのまま実行する
	 *                                 				True:実行せずSQL文を返す
	 *                                 															
	 * @return Mixed	$no_executeがfalseの場合、成功ならTrue。$no_executeがTrueならSQL文だけ返す
	 */
	function sqlExecute($data,$action,$no_execute=false)
	{
		$sql = null;
		$name_list = "";
		$prepare_list="";
		$value_list=array();
		//カラム指定がある場合(DELETEはなし)
		if (isset($data['column']) === true && is_array($data['column']) === true) {
			foreach ($data['column'] as $key=>$value) {
				switch ($action) {
					case "ins":
					case "rep":
						if ($name_list === "") {
							$name_list	= "`{$key}`";
							$prepare_list	= "?";
							$value_list[] = $value;
						} else {
							$name_list	.= ", `{$key}`";
							$prepare_list	.= ", ?";
							$value_list[] = $value;
						}
						break;

					case "upd":
						if (strpos($key, ".") == false) {
							if ($prepare_list === "") {
								$prepare_list = "`{$key}` = ?";
								$value_list[] = $value;
							} else {
								$prepare_list .= ", `{$key}` = ?";
								$value_list[] = $value;
							}
						} else {
							if ($prepare_list === "") {
								$prepare_list = "$key = ?";
								$value_list[] = $value;
							} else {
								$prepare_list .= ",$key = ?";
								$value_list[] = $value;
							}
						}
						break;

					default:
						break;

				}
			}
		}

		$where_prepare='';
		if (isset($data['where']) === true && is_array($data['where']) === true) {
				foreach ($data['where'] as $key=>$value) {
					if ($where_prepare === '') {
						$where_prepare = "$key = ?";
						$value_list[] = $value;
					} else {
						$where_prepare .= " AND $key = ?" ;
						$value_list[] = $value;
					}
				}
			} 
		if ($where_prepare !== "") {
			$where_prepare = " WHERE {$where_prepare}";
		}

		$ignore = "";
		if (isset($data['ignore']) == true && $data['ignore'] == true) {
			$ignore = " IGNORE";
		}

		switch ($action) {
		// レコードの挿入
		case "ins":
			$sql = "INSERT $ignore INTO {$data['table']} ($name_list) VALUES($prepare_list);";
			break;
		// 置換
		case "rep":
			$sql = "REPLACE INTO {$data['table']} ($name_list) VALUES($prepare_list);";
			break;
		// 更新
		case "upd":
			$sql = "UPDATE $ignore {$data['table']} SET $prepare_list $where_prepare $extension;";
			break;
		// 削除
		case "del":
			$sql = "DELETE FROM {$data['table']} $where_prepare $extension;";
			break;
		}

		if (!$no_execute) {
			return $this->setSqlExecute($sql,$value_list);
		} else {
			return $sql;
		}




	}

/**
 * 登録されたSQL文と、値のリストを実行する
 * @param String $sql        SQL文
 * @param Array $value_list 実行したい値のリスト
 */
	function setSqlExecute($sql,$value_list)
	{
		// 実行
		$this->db->beginTransaction();
		try{

			$stmt = $this->db->prepare($sql);
			for ($i=0;$i < count($value_list); $i++){
				$type = $this->typeSelection($value_list[$i]);
				$stmt->bindParam($i+1, $value_list[$i], $type);
			}
			//成功ならTrue
			$result = $stmt->execute();

			$this->db->commit();
		} catch(Excetipn $e){
		 $db->rollBack();
		};
		
		if(!$result) {
			$this->sqlError($sql, $result);
			$ret = null;
		} else {
			$ret = true;
		}
		return $ret;
	}


	/**
	 * 指定したテーブルの全てを取得
	 * @param  String $table テーブル名
	 * @param  String $order_by 順番の指定。
	 * @return Array        取得したデータの配列
	 */
	public function getAll($data)
	{
			$orderby='';
			if (isset($data['order_column']) == true && isset($data['order_seq'])== true ) {
				$orderby = "order by {$data['order_column']} {$data['order_seq']}";
			}

			$sql = "SELECT * FROM " . $data['table'] . " " . $orderby;

		$stmt = $this->db->query($sql);
	//	$stmt = $this->db->query("select * from $table order by `id_name` $order_by");
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}




	/**
	 * bindParam時に必要な型を自動で選択する
	 * @ref http://d.hatena.ne.jp/uunfo/20090204/1233728629
	 * @param  Mixed $bind 型を判別したい変数
	 * @return String      判別された型 
	 */
	public function typeSelection($bind){
		$type = \PDO::PARAM_STR;
		switch(true){
			case is_bool($bind) :
				$type = \PDO::PARAM_BOOL;
				break;
			case is_null($bind) :
				$type = \PDO::PARAM_NULL;
				break;
			case is_int($bind) :
				$type = \PDO::PARAM_INT;
				break;
			case is_float($bind) :
			case is_numeric($bind) :
			case is_string($bind) :
			default:
				$type = \PDO::PARAM_STR;
				break;
		}
		return $type;

	}


	function sqlError($sql, &$result = null,$exit_flg=1)
	{
		$trace_str   = "";
		$trace_array = debug_backtrace();
		if (count($trace_array) > 0)
		{
			for ($i=0; $i < count($trace_array)-1; $i++)
			{
				$trace_str .= "file：" . (isset($trace_array[$i]['file']) ? $trace_array[$i]['file'] : '') . "\n";
				$trace_str .= "line：" . (isset($trace_array[$i]['line']) ? $trace_array[$i]['line'] : '') . "\n";
				if (isset($trace_array[1]))
					$trace_str .= "function：" . (isset($trace_array[$i+1]['function']) ? $trace_array[$i+1]['function'] : '') . "\n\n";
			}
		}

		if (DEBUG_MODE) {
			$err_sql = nl2br($sql);
			print "<div>";
			print "<h1>Error Report</h1>";
			print "<p>SQL<br>$err_sql</p>";
			print "<hr><p>Error info<br>".$this->db->errorInfo()."</p>";
			print "<hr><p>Error code<br>".$this->db->errorCode()."</p>";
			print nl2br($trace_str) . "<br>";
			print "</div>";
		}

		if ($exit_flg == 1){
			exit;
		}
	}




}
