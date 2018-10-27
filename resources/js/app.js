
import Vue from "vue";
require('./materialize.min');

window.axios = require('axios');
import MainView from "./components/main-view";


const vm = new Vue({
    el: "#app",
    components: {
        'main-view': MainView
    }
});
  

  

//   /**
//    * 進捗バーの色は進捗率によって変わるので、その変更をする
//    * @return {void}
//    */
//   function setColor(){
  
//     const bars = document.getElementsByClassName('bar');
  
//     for (let i=0;i<=bars.length;i++){
//       if (bars[i]){
//         let p_rate = Number(bars[i].textContent.replace('%', ""));
//         colChange(bars[i], p_rate);
//       }
//     }
//   }
  
  
  
//   /**
//    * プログレスバーの色を変化させる
//    * @param  {Object} sel    変化させる要素オブジェクト
//    * @param  {integer} p_rate 進捗率
//    * @return {void}        
//    */
//   function colChange(sel, p_rate) {
  
//       if (p_rate < 20) {
//         sel.style.backgroundColor='#ff7575';
//       } else if (p_rate < 30) {
//           sel.style.backgroundColor='Orange';
//       } else if (p_rate < 50) {
//           sel.style.backgroundColor='#ffe575';
//       } else if (p_rate < 75) {
//           sel.style.backgroundColor='#d4f442';
//       } else {
//           sel.style.backgroundColor='#acff28';
//       }
//       if (p_rate == 100) {
//           sel.parentNode.classList.remove('stripes');
//           sel.parentNode.classList.remove('orange');
//           sel.parentNode.classList.remove('shine');
//       } else {
//         if (!sel.classList.contains("stripes")) sel.parentNode.classList.add('stripes');
//         if (!sel.classList.contains("orange")) sel.parentNode.classList.add('orange');
//         if (!sel.classList.contains("stripes")) sel.parentNode.classList.add('shine');
  
//       }
  
//   }
  
  
//   /**
//    * Ajax関数。
//    * @param  {Object} sendData  POSTメソッドで送りたいnameとvalueのオブジェクト
//    * @return {Promise}  Promiseオブジェクト
//    */
//   function ajaxPlatform(sendData){
//     return new Promise(function (resolve, reject) {
  
//     let params = new URLSearchParams();
//     if (typeof sendData == "object") {
//       for(let key in sendData){
//         params.append(key, sendData[key]);
//       }  
//     }
//     axios.post('./ajax.php', params)
//       .then(response => {
//           resolve(response);
//       }).catch(error => {
//           reject('errorが発生しました');
//       });
  
//       }); 
//   }

