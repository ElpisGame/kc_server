(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-6e2dd763"],{"4e1f":function(e,t,l){},"6bcf":function(e,t,l){"use strict";l.r(t);var a=function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("div",[l("div",{staticStyle:{background:"#f9f9f9",width:"350px",padding:"20px",margin:"10px"}},[l("el-form",{attrs:{inline:!0,rules:e.formRules,"label-width":"80px"}},[l("el-form-item",{attrs:{label:"执行命令"}},[l("el-select",{attrs:{placeholder:"请选择"},model:{value:e.cmd,callback:function(t){e.cmd=t},expression:"cmd"}},e._l(e.commands,(function(e){return l("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1),e._v(" "),l("el-button",{attrs:{size:"",type:"primary",plain:""},on:{click:function(t){return e.execute()}}},[e._v(" 执行 ")])],1)],1)],1)])},n=[],c=l("424b"),s=l("5c96"),i={name:"Shell",data:()=>({commands:[{label:"更新资源",value:"updateall"}],cmd:"",output:"",formRules:{}}),mounted(){},methods:{execute:function(){let e=s["Loading"].service({fullscreen:!0});c["a"].get("/api/game/shell",{cmd:this.cmd}).then(e=>{console.log(e),0===e.data?this.$message.error("执行失败"):this.$message.success("执行成功")}).finally(()=>{e.close()})}}},o=i,u=(l("bc95"),l("2877")),r=Object(u["a"])(o,a,n,!1,null,"570992f6",null);t["default"]=r.exports},bc95:function(e,t,l){"use strict";l("4e1f")}}]);