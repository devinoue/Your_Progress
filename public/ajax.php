<?php

require_once "config.php"; //設定
require_once "Request.php"; //フォームからのREQUEST等を処理する
require_once "db.php"; //データベースへの接続とSQL文の作成など
require_once "functions.php"; //エラーハンドラーなどその他

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
}

//ユーザー定義のエラーハンドラー
set_error_handler('userErrorHandler');

use Ronbun\DB;

/**
 * Your Progressメインクラス
 * @access public
 * @author Masaharu Inoue <pesteur1822@gamil.com>
 */
class YourProgress
{

    public $request = null;
    public $tasks = [];
    public $db = null;

/**
 * @constructor
 */
    public function __construct()
    {

        $this->db = new DB();
        $this->request = new Request();

    }

    /**
     * デフォルトの処理を行うメソッド
     * @return void
     */
    public function index()
    {
        $this->tasks = $this->db->getAll([
            'table' => 'tasks',
            'order_column' => 'id_name',
            'order_seq' => 'desc',
        ]);

        $response = json_encode($this->tasks);
        header("Content-Type: application/json; charset=utf-8");
        echo $response;
    }

/**
 * 実行処理のコントロール
 * 受け取ったjobが実在するメソッドなら実行
 * @return void
 */
    public function execute()
    {
        $job = isset($_REQUEST["job"]) ? $_REQUEST["job"] : null;

        if (method_exists($this, $job)) {
            //指定されたjobを実行する通常の処理
            $this->$job();

        } else if (method_exists($this, 'index')) {
            //デフォルトの処理
            $this->index();
        } else {
            //ない機能が呼び出された

        }

    }

/**
 * タスクの追加
 * @return void
 */
    private function entryTodo()
    {
        $p_id = $this->request->get('p_id');
        $todo_line = $this->request->get('todo_line');

        $timestamp = time();

        $column['p_name'] = $p_id;
        $column['id_name'] = $timestamp;
        $column['task_name'] = $todo_line;
        $column['progress'] = 0;

        //SQL登録
        $result = $this->db->sqlExecute(['column' => $column, 'table' => 'tasks'], 'ins');

        if ($result === null) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(['error' => '新規登録に失敗しました。']);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(['timestamp' => $timestamp]);
        }

        return;
    }

/**
 * 進捗率の更新
 * @return void
 */
    private function changeTodo()
    {
        $p_id = $this->request->get('p_id');

        $column['progress'] = intval($this->request->get('p_rate'));
        $where['id_name'] = $this->request->get('t_id');

        //SQL実行
        $result = $this->db->sqlExecute(['column' => $column, 'table' => 'tasks', 'where' => $where], 'upd');

        if ($result === null) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(['error' => '進捗率変更に失敗しました。']);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(['mes' => '進捗率変更しました']);
        }

        return;

    }

/**
 * 削除処理
 * @return void
 */
    private function deleteTodo()
    {
        $where['id_name'] = $this->request->get('del_id');

        //SQL実行
        $result = $this->db->sqlExecute(['table' => 'tasks', 'where' => $where], 'del');

        if ($result === null) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(['error' => '削除に失敗しました。']);
        } else {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode(['mes' => '削除しました']);
        }
        return;
    }

}

$your_progress = new YourProgress();
$your_progress->execute();

exit;
