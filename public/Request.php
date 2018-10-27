<?php
/**
 * フォームからのリクエストをこのオブジェクトに集めることで、
 * 他のクラスからも取り扱えるようにするためのクラスです。
 * Todo:Validation
 */
class Request
{
	private $_req = array();

	public function Request()
	{
		if (is_array($_REQUEST) === true) {
			$this->_req = $_REQUEST;
		}
	}
	
	/**
	*データを追加する
	*
	*@access	public
	*@param		String		$name		連想配列のキーを指定
	*@param					$data		格納するデータ
	*@return							なし
	*/
	public function add($name, $data)
	{
		$this->_req[$name] = $data;
	}

	/**
	*リクエストのあったデータを返す
	*
	*@access	public
	*@param		String		$name		取得したい変数の名前
	*@return							リクエストのあった変数の値、無ければNULLを返す
	*/
	public function get($name)
	{
		if (isset($this->_req[$name]) === true) {
			return $this->_req[$name];
		} else {
			return NULL;
		}
	}
	
	//Todo
	//abstract protected function checkType($name);

	
}
