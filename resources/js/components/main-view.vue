<template>
<div>
    		<div class="jumbotron">
			<div class="container">
				あなたの進捗率 :
				<span id="r_persent">0</span>%
				<br>
				<span id="message"></span>
			</div>
			<!--container end-->
		</div>
		<!--jumbotron end-->
		<!--start entry form-->

		<div class="container">
			<div class="row">
				<div class="col m3"></div>
				<div class="input-field col m6 s12">
					<input id="todo_line" type="text" size="40" name="todo_line" class="form-control validate" @keyup.enter="chkFormEntry" />
					<label for="todo_line">新しいTODO</label>
					<a class="btn-floating btn-large pulse  amber accent-4 z-depth-3" id="new_todo" @click="chkFormEntry">
  <i class="material-icons">create</i></a>
					<span class="p_id clearText">1234</span>
				</div>
				<!--end input-field -->
				<div class="col m3"></div>
			</div><!-- end entry form row-->
		</div><!-- /container -->


		<div class="container">
			<div class="row">
				<div class="col m3"></div>
				<div class="input-field col m6 s12">
          <div class="btn" @click="changeView(1)">終了済み</div>
          <div class="btn" @click="changeView(0)">未達成</div>
          <div class="btn" @click="toCompleted()">100%のタスクを完了済みにする</div>
				</div>
				<!--end input-field -->
				<div class="col m3"></div>
			</div><!-- end entry form row-->
		</div><!-- /container -->





<transition-group name="task-list" tag="div">
<div v-for="(task,index) in tasks" v-if="task.state == mode" v-bind:key="task.id_name" class="container task-items">
	<div class="row" :key="task.id_name">
				<div class="col m3 s12">
					<span class="todo_name">{{task.task_name}}</span>
					<br>
					<span class="mes"></span>
					<span class="del" :value="task.id_name"></span>
					<i class="material-icons pointer" @click="deleteLine(index,task.id_name,$event)">delete_forever</i>
					<span class="p_id clearText">{{task.p_name}}</span>
				</div>
				<!--three-->
				<div class="col m9 s12">
					<!-- progress bar -->
					<div class="nprogress-bar shine stripes orange">
						<span class="bar" :style="{width: task.progress + '%'}">{{task.progress}}%</span>
					</div>
					<div class="range-field">
						<input type="range" :value="task.progress" @change="sendProgress($event)" @input="changeValue(index, $event)" step="10" min="0" max="100" />
					</div>
					<!-- end progress bar -->
					<span class="id clearText">{{task.id_name}}</span>
					<span class="p_id clearText">{{task.p_name}}</span>
				</div>
				<!--/nine-->
	</div>
</div><!-- /container -->
</transition-group>
</div>
</template>

<script>
export default {
  data: () => ({
    tasks: [],
    mode: 0,
    flg: false
  }),
  created: function() {
    //ajax処理
    axios
      .get("http://localhost/_your_progress/public/api")
      .then(r => {
        this.tasks = r.data;
        this.totalProgressRate();
      })
      .catch(error => {
        console.log(error);
      });
  },
  updated: function() {
    if (this.flg == false) {
      this.setColor();
      this.flg = true;
    }
  },

  methods: {
    //完了済みタスクへ変える
    toCompleted() {
      const sendData = {
        job: "changeCompleted"
      };
    },
    changeView(e) {
      this.flg = false;
      this.mode = e;
    },

    changeValue(index, e) {
      this.$set(this.tasks[index], "progress", e.target.value);
      this.totalProgressRate();
    },
    sendProgress(e) {
      const p_rate = e.target.value;
      const t_id = e.target.parentNode.parentNode.querySelector(".id")
        .textContent;
      const p_id = e.target.parentNode.parentNode.querySelector(".p_id")
        .textContent;

      const sendData = {
        _method: "PATCH",
        p_id: p_id,
        t_id: t_id,
        p_rate: p_rate
      };

      const self = this;
      this.ajaxPlatform(sendData,t_id)
        .then(function(res) {
          //e.target.parentNode.parentNode.parentNode.querySelector(".mes").textContent = "進捗率を変更しました";
          M.toast({ html: "進捗率を変更しました" });
          self.colChange(
            e.target.parentNode.parentNode.querySelector(".bar"),
            p_rate
          );
          console.dir(res.data);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    totalProgressRate() {
      let c = 0;
      let total_num = 0;
      for (let task of this.tasks) {
        if (task.state == 0) {
          c += Number(task.progress);
          total_num++;
        }
      }
      const r = document.getElementById("r_persent");
      if (total_num != 0) {
        r.textContent = Math.floor(c / total_num);
      } else {
        r.textContent = 0;
      }

      // トップの数字を更新
      const m = document.getElementById("message");
      if (r.textContent >= 100) {
        m.textContent = "おめでとうございます！ 全てのタスクが終わりました！";
      } else {
        m.textContent = "";
      }
    },
    chkFormEntry(e) {
      const todo_line_el = document.getElementById("todo_line");
      const p_id = todo_line_el.parentNode.querySelector(".p_id").textContent;
      const todo_line = todo_line_el.value;
      if (todo_line == "" || p_id == "") {
        alert("入力がされていません");
        return;
      }
      todo_line_el.value = "";

      const sendData = {
        p_id: p_id,
        todo_line: todo_line
      };
      const self = this;

      this.ajaxPlatform(sendData,"")
        .then(function(res) {
          self.tasks.unshift({
            id_name: res.data.timestamp,
            task_name: todo_line,
            progress: 0,
            state: 0
          });
          //vm.tasks.splice(0,0,{ "id_name": res.data.timestamp, "task_name": todo_line, "progress": 0 });
          //vm.$set(vm.tasks, 0,  { "id_name": res.data.timestamp, "task_name": todo_line, "progress": 0 });//追加ではなく、0の値と置き換えてる

          self.$forceUpdate();

          M.toast({ html: "タスクを追加しました" });
          console.log("送信したテキスト: " + res);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    deleteLine(index, del_id, e) {
        if (window.confirm("削除しますか？") == false){
            return;
        }
      const p_id = e.target.parentNode.querySelector(".p_id").textContent;
      this.tasks.splice(index, 1);

      const sendData = {
        _method: "delete",
        p_id: p_id,
        del_id: del_id
      };

      this.ajaxPlatform(sendData,del_id)
        .then(function(res) {
          console.log("送信したテキスト: " + res);
          M.toast({ html: "TODOを削除しました" });
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    setColor() {
      const bars = document.getElementsByClassName("bar");

      for (let i = 0; i <= bars.length; i++) {
        if (bars[i]) {
            let p_rate = Number(bars[i].textContent.replace("%", ""));
            this.colChange(bars[i], p_rate);
        }
      }
    },
    colChange(sel, p_rate) {
      if (p_rate < 20) {
        sel.style.backgroundColor = "#ff7575";
      } else if (p_rate < 30) {
        sel.style.backgroundColor = "Orange";
      } else if (p_rate < 50) {
        sel.style.backgroundColor = "#ffe575";
      } else if (p_rate < 75) {
        sel.style.backgroundColor = "#d4f442";
      } else {
        sel.style.backgroundColor = "#acff28";
      }
      if (p_rate == 100) {
        sel.parentNode.classList.remove("stripes");
        sel.parentNode.classList.remove("orange");
        sel.parentNode.classList.remove("shine");
      } else {
        if (!sel.classList.contains("stripes"))
          sel.parentNode.classList.add("stripes");
        if (!sel.classList.contains("orange"))
          sel.parentNode.classList.add("orange");
        if (!sel.classList.contains("stripes"))
          sel.parentNode.classList.add("shine");
      }
    },
    ajaxPlatform(sendData,query) {
      return new Promise(function(resolve, reject) {
        let params = new URLSearchParams();
        if (typeof sendData == "object") {
          for (let key in sendData) {
            params.append(key, sendData[key]);
          }
        }
        if (query != "") query = "/" + query;
        axios
          .post("http://localhost/_your_progress/public/api" + query, params)
          .then(response => {
            resolve(response);
          })
          .catch(error => {
            reject("errorが発生しました");
          });
      });
    }
  }
};
</script>

<style>
</style>
