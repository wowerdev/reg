"use strict";window.addEventListener("load",function(){window.Req=function(){this.send=function(e){var t=new XMLHttpRequest;t.onreadystatechange=function(){1==t.readyState&&e.cb_before&&e.cb_before()},t.onload=t.onerror=function(){e.cb_after&&e.cb_after(),200!=t.status?console.error("Error: ".concat(t.status," ").concat(t.statusText)):e.cb_success&&e.cb_success(t)},t.open("POST",e.url,!0),e.data?t.send(e.data):t.send()},this.getData=function(e){return new FormData(e)}},window.showBanner=function(e){var t=document.querySelector("main"),n=document.createElement("div"),o=document.createElement("span");n.classList.add("alert"),o.classList.add("alert__close"),o.textContent="x",n.innerHTML=e,n.appendChild(o),t.appendChild(n),o.addEventListener("click",function(){t.removeChild(n)})},window.Reg=function(){var n=document.querySelector(".reg__form"),o=document.querySelector(".reg");this.send=function(e){e&&e.preventDefault();var t=new Req;t.send({url:"index.php",data:t.getData(n),cb_before:function(){o.classList.add("load")},cb_after:function(){o.classList.remove("load")},cb_success:function(e){showBanner(e.responseText)}})},n.addEventListener("submit",this.send)},document.querySelector(".reg__form")&&new Reg});