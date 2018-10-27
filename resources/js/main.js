
window.onload = function() {
  
  const vm = new Vue({
    el: "#app",
    data: {
      tasks: [],
      mode: 0,
      flg : false,
    },
    created: function() {
      //ajax処理
      axios.get("./ajax.php").then((r) => {
        this.tasks = r.data;
        this.totalProgressRate();
      }).catch(error => {
        console.log(error);
      });


    },
    updated: function() {
      if (this.flg == false) {
        setColor();
        this.flg = true;
      }
    },

    methods: {
      //完了済みタスクへ変える
      toCompleted(){
        const sendData = {
          'job': 'changeCompleted',
        };
        
      },
      changeView(e){
        this.flg = false;
        this.mode=e;
      },
      test() {

      },
      changeValue(index, e) {
        this.$set(this.tasks[index], 'progress', e.target.value);
        this.totalProgressRate();
      },
      sendProgress(e) {
        const p_rate = e.target.value;
        const t_id = e.target.parentNode.parentNode.querySelector(".id").textContent;
        const p_id = e.target.parentNode.parentNode.querySelector(".p_id").textContent;

        const sendData = {
          'job': 'changeTodo',
          'p_id': p_id,
          't_id': t_id,
          'p_rate': p_rate
        };

        ajaxPlatform(sendData).then(function(res) {
          //e.target.parentNode.parentNode.parentNode.querySelector(".mes").textContent = "進捗率を変更しました";
          M.toast({ html: '進捗率を変更しました' });

          colChange(e.target.parentNode.parentNode.querySelector(".bar"), p_rate);
          console.dir(res);
        }).catch(function(error) {
          console.log(error);
        });


      },
      totalProgressRate() {
        let c = 0;
        for (let task of this.tasks) {
          c += Number(task.progress);
        }
        const r = document.getElementById('r_persent');
        if (this.tasks.length != 0) {
          r.textContent = Math.floor(c / this.tasks.length);
        } else {
          r.textContent = 0;
        }

        // トップの数字を更新
        const m = document.getElementById('message');
        if (r.textContent >= 100) {
          m.textContent = "おめでとうございます！ 全てのタスクが終わりました！"
        } else {
          m.textContent = ""
        }


      },
      chkFormEntry(e) {

        const todo_line_el = document.getElementById('todo_line');
        const p_id = todo_line_el.parentNode.querySelector(".p_id").textContent;
        const todo_line = todo_line_el.value;
        if (todo_line == '' || p_id == '') {
          alert('入力がされていません');
          return;
        }
        todo_line_el.value = "";


        const sendData = {
          'job': 'entryTodo',
          'p_id': p_id,
          'todo_line': todo_line,
        };

        ajaxPlatform(sendData).then(function(res) {
          vm.tasks.unshift({ "id_name": res.data.timestamp, "task_name": todo_line, "progress": 0 ,"state" : 0});
          //vm.tasks.splice(0,0,{ "id_name": res.data.timestamp, "task_name": todo_line, "progress": 0 });
          //vm.$set(vm.tasks, 0,  { "id_name": res.data.timestamp, "task_name": todo_line, "progress": 0 });//追加ではなく、0の値と置き換えてる


          vm.$forceUpdate();



          M.toast({ html: 'タスクを追加しました' });
          console.log('送信したテキスト: ' + res);
        }).catch(function(error) {
          console.log(error);
        });

      },
      deleteLine(index, del_id, e) {
        const p_id = e.target.parentNode.querySelector(".p_id").textContent;
        this.tasks.splice(index, 1);


        const sendData = {
          'job': 'deleteTodo',
          'p_id': p_id,
          'del_id': del_id,
        };

        ajaxPlatform(sendData).then(function(res) {
          console.log('送信したテキスト: ' + res);
          M.toast({ html: 'TODOを削除しました' });
        }).catch(function(error) {
          console.log(error);
        });
      }
    }
  });

}//onload

/**
 * 進捗バーの色は進捗率によって変わるので、その変更をする
 * @return {void}
 */
function setColor(){

  const bars = document.getElementsByClassName('bar');

  for (let i=0;i<=bars.length;i++){
    if (bars[i]){
      let p_rate = Number(bars[i].textContent.replace('%', ""));
      colChange(bars[i], p_rate);
    }
  }
}



/**
 * プログレスバーの色を変化させる
 * @param  {Object} sel    変化させる要素オブジェクト
 * @param  {integer} p_rate 進捗率
 * @return {void}        
 */
function colChange(sel, p_rate) {

    if (p_rate < 20) {
      sel.style.backgroundColor='#ff7575';
    } else if (p_rate < 30) {
        sel.style.backgroundColor='Orange';
    } else if (p_rate < 50) {
        sel.style.backgroundColor='#ffe575';
    } else if (p_rate < 75) {
        sel.style.backgroundColor='#d4f442';
    } else {
        sel.style.backgroundColor='#acff28';
    }
    if (p_rate == 100) {
        sel.parentNode.classList.remove('stripes');
        sel.parentNode.classList.remove('orange');
        sel.parentNode.classList.remove('shine');
    } else {
      if (!sel.classList.contains("stripes")) sel.parentNode.classList.add('stripes');
      if (!sel.classList.contains("orange")) sel.parentNode.classList.add('orange');
      if (!sel.classList.contains("stripes")) sel.parentNode.classList.add('shine');

    }

}


/**
 * Ajax関数。
 * @param  {Object} sendData  POSTメソッドで送りたいnameとvalueのオブジェクト
 * @return {Promise}  Promiseオブジェクト
 */
function ajaxPlatform(sendData){
  return new Promise(function (resolve, reject) {

  let params = new URLSearchParams();
  if (typeof sendData == "object") {
    for(let key in sendData){
      params.append(key, sendData[key]);
    }  
  }
  axios.post('./ajax.php', params)
    .then(response => {
        resolve(response);
    }).catch(error => {
        reject('errorが発生しました');
    });

    }); 
}
