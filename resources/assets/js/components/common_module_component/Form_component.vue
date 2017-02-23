<template >
  <div>
    <div class="row">
      <div class="col-sm-6">
        <button v-if="readOnly && !isLoading.length" v-on:click="entry_id = -1" v-bind:disabled="isLoading.length ? true : false" type="button" class="btn btn-default"><i class="fa fa-plus" aria-hidden="true"></i> Create</button>
        <button v-if="entry_id > 0 && readOnly==true && !isLoading.length" v-bind:disabled="isLoading.length ? true : false" v-on:click="readOnly=false;formDisabled=false;" type="button" class="btn btn-default"><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>
        <button v-if="(entry_id > 0 && readOnly==true && !isLoading.length)" v-bind:disabled="isLoading.length ? true : false" v-on:click="deleteEntry" type="button" class="btn btn-default"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>

      </div>
      <div class="col-sm-6 align-right">
        <button type="button" class="btn btn-default"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
        <button v-if="readOnly==true && !isLoading.length"
          v-on:click="$emit('change-entry', 0)"
          type="button" class="btn btn-default">
          <i class="fa fa-step-backward" aria-hidden="true"></i>
        </button>
        <button v-if="readOnly==true && !isLoading.length" v-on:click="$emit('change-entry', 1)" type="button" class="btn btn-default"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
        <button v-if="readOnly==true && !isLoading.length" v-on:click="$emit('change-entry', 2)" type="button" class="btn btn-default"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
        <button v-if="readOnly==true && !isLoading.length" v-on:click="$emit('change-entry', 3)" type="button" class="btn btn-default"><i class="fa fa-step-forward" aria-hidden="true"></i></button>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="panel panel-default" >
          <div class="panel-heading">{{formSetting.title}}<span v-if="isLoading.length" class="pull-right"><i  class="fa fa-hourglass-half" aria-hidden="true"></i> Loading...</span></div>
          <div class="panel-body" tabIndex="-1">
            <form class="form-horizontal">
             <form-input
                v-on:input-changed="updateFormValue"
                :read_only="readOnly"
                :form_disabled="formDisabled"
                :input_values="formValue"
                :input_settings="formInputSetting"
                :is_loading="isLoading"
                >
              </form-input>
              <div class="row">
                <div class="form-group">
                  <label class="control-label col-sm-3" >Last Updated</label>
                  <div class="col-sm-4"  >
                    <input class="form-control" v-model="updatedAtTimestamp" disabled="true">
                  </div>
                </div>
              </div>
              <div class="row" >
                <div class="col-sm-12 text-right" v-if="!readOnly">
                  <button v-if="this.entry_id <= 0" class="btn btn-default pull-left" v-on:click="submitForm('addmore')" type="button" v-bind:disabled="isLoading.length ? true : false" >
                    <i class="fa fa-plus-square" aria-hidden="true"></i> Add more
                  </button>
                  <button class="btn btn-primary " v-on:click="submitForm" type="button" v-bind:disabled="isLoading.length ? true : false" >
                    <span v-if="this.entry_id > 0"><i class="fa fa-save" aria-hidden="true"></i> SAVE</span>
                    <span v-else><i class="fa fa-plus" aria-hidden="true"></i>  CREATE</span>
                  </button>
                  <button class="btn btn-default" v-on:click="cancelForm" type="button" v-bind:disabled="isLoading.length ? true : false">{{this.entry_id > 0 ? "Close" : "Cancel"}}</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default{
    created(){
      this.initFormSetting();
      this.formInputSetting = this.initFormInputSetting(this.form_input_setting);
    },
    components :{
      "form-input" : require('./Form_input.vue'),
    },
    data(){
      return {
        entry_id : 0,// -1 create, 0 no action
        formSetting : {},
        formInputSetting : {},
        formValue : {},
        isLoading : [],
        readOnly : this.read_only,
        formDisabled : true,
        formValuePath : {},
        updatedAtTimestamp : "",
        showEntryRequest : null
      };
    },
    props : {
      id : Number,
      form_setting : Object,
      form_input_setting : Object,
      api : Object,
      read_only : Boolean

    },
    watch : {
      entry_id : function(value){
        value = value*1;
        console.log("entry_id"+value)
        if(value === -1){//create
          for(let key in this.formValue){
            if(key !== "id"){
              this.updateFormValue(key, null);
              let setting = this.formInputSetting;
              for(let pathIndex in this.formValuePath[key]){
                setting = setting[this.formValuePath[key][pathIndex]]["input_setting"];
              }
              if(setting[key]["select_option"] && setting[key]["select_option"]["option_function"]){
                setting[key]["select_option"]["option_function"](setting, this);
              }
            }
          }
          this.updatedAtTimestamp = "";
          this.readOnly = false;
          this.formDisabled = false;
        }
      },
      id : function(value){
        this.entry_id = value;
        value = value*1;
        if(value*1 > 0){
          this.showEntry();
        }
        if(value == 0){
          this.formDisabled = true;
          this.cancelForm();
        }else{
          this.formDisabled = false;
        }
      },
      read_only : function(value){
        this.readOnly = value;
      }
    },
    methods : {
      initFormSetting(){
        this.formSetting = this.form_setting;
        (this.formSetting.title === undefined) ? Vue.set(this.formSetting, 'title', this.formSetting.api +" Detail") : null;
      },
      updateFormValue(name, value){
        if(name === "id") return false;
        let setting = this.formInputSetting
        for(let pathIndex in this.formValuePath[name]){
          setting = setting[this.formValuePath[name][pathIndex]]["input_setting"];
        }
        let formattedValue = this.dataFormat(value, setting[name]);
        Vue.set(this.formValue, name, formattedValue);

        if(setting[name]["value_changed"]){
          setting[name]["value_changed"](this.formValue, this);
        }
      },
      deleteEntry(){
        this.isLoading.push("deleteEntry");

        if(!this.formValue["locked"]){
          bootbox.confirm({
            size: 'small',
            title : "Are you sure you want to delete this entry?",
            message: 'Once this entry is deleted, it can no longer be recovered',
            buttons: {
              cancel: {
                  label: 'No',
                  className: 'btn-default'
              },
              confirm: {
                  label: 'Yes, delete this entry',
                  className: 'btn-danger'
              },
            },
            callback : (result) => {
              if(result){
                let requestOption = {
                  params : {
                    "id" : this.entry_id
                  }
                };
                this.$http.get(this.api.delete, requestOption).then((response) => {
                  // success callback
                  let result = response.body;
                  if(result.data){
                    this.$emit("entry-deleted", this.entry_id);
                    this.entry_id=-1;
                    this.cancelForm();
                  }else{
                  }
                  this.isLoading.pop();
                });
              }else{
                this.isLoading.pop();
              }
            }
          })
        }else{
          bootbox.alert("This entry is locked! Contact the admin regarding this matter", ()=>{
            this.isLoading.pop();
          });

        }


      },
      showEntry(){
        this.isLoading.push("showEntry");
        if(this.showEntryRequest){

          this.showEntryRequest.abort();
        }
        let requestOption = {
          before : function(xhr){
            this.showEntryRequest = xhr;
          },
          params : {
            "id" : this.entry_id
          }
        };
        // this.emptyFormValue();
        this.$http.get(this.api.retrieve, requestOption).then((response) => {
          // success callback
          let result = response.body;
          if(result.data){
            for(let key in this.formValue){
              if(key !== "id" && typeof result.data[key] !== "undefined"){
                this.updateFormValue(key, result.data[key]);
                let setting = this.formInputSetting;
                for(let pathIndex in this.formValuePath[key]){
                  setting = setting[this.formValuePath[key][pathIndex]]["input_setting"];
                }
                if(setting[key]["select_option"] && setting[key]["select_option"]["option_function"]){
                  setting[key]["select_option"]["option_function"](setting, this);
                }
              }
            }
            this.updatedAtTimestamp = result.data["updated_at"]
          }
          this.showEntryRequest = null;
          this.isLoading.pop();
        });
      },
      emptyFormValue(){

        for(let x in this.formValue){
          if(x !== "id"){
            this.updateFormValue(x, null);
          }
        }
        this.updatedAtTimestamp = "";
      },
      submitForm(option){
        if(typeof option === "undefined"){
          option = "";
        }
        this.isLoading.push("submitForm");
        let requestOption = {
          params : this.formValue
        };
        let link = this.api.create;
        if(this.entry_id > 0){
          requestOption.params.id = this.entry_id;
          link = this.api.update;
        }
        this.$http.get(link, requestOption).then((response) => {
          // success callback
          let result = response.body;
          if(result.data){
            if(this.entry_id === -1){
              this.entry_id = result.data;
              this.$emit("entry-created", this.entry_id);
              if(option === "addmore"){
                this.entry_id = -1;
                this.emptyFormValue();
              }else{
                this.updatedAtTimestamp = result.request_timestamp;
                this.readOnly = true;
              }
            }else{
              this.$emit("entry-updated", this.entry_id);
              this.updatedAtTimestamp = result.request_timestamp;
              this.readOnly = true;
            }

          }else{
            var message = "";
            if(result.error.status === 100){
              for(var x in result.error.message){
                message += '<i class="fa text-danger fa-chevron-right" aria-hidden="true"></i> '+result.error.message[x][0]+"<br>"
              }
            }
            bootbox.alert({
              size: 'small',
              title: '<span class="text-danger"><i class="fa fa-warning" aria-hidden="true"></i> Operation Failed!</span>',
              closeButton : false,
              message : message
            })
          }
          this.isLoading.pop();
        });
      },
      cancelForm(){
        if(this.entry_id === -1){
          this.emptyFormValue();

          this.entry_id = 0;
          this.formDisabled = true;
        }
        this.readOnly = true;
      },
      initFormInputSetting(formInputSetting, path){
        if(typeof path === "undefined"){
          path = [];
        }
        let currentPath = path.slice(0);
        let settings = {};
        for(let db_name in formInputSetting){
          let input = {};
          if(db_name.indexOf("dummy") > -1){//init dummy
            input.type = "dummy"
            input.label = "_"
            input.label_style = {
              color : "white"
            }
            input.col = (formInputSetting[db_name]["col"]) ? formInputSetting[db_name]["col"]*1 : 12;
            input.col_label = (formInputSetting[db_name]["col_label"]) ? formInputSetting[db_name]["col_label"] : (input.col === 6 ? 6 : 3);
            input.col_input ='col-sm-'+(12 -input.col_label);
            settings[db_name] = input;
          }else if(db_name.indexOf("form_group") === -1){//init input
            Vue.set(input, 'disabled', false);
            Vue.set(input, "name", (formInputSetting[db_name]["name"]) ? formInputSetting[db_name]["name"] : this.underscoreToWord(db_name));

            Vue.set(input, "db_name", db_name);
            Vue.set(input, "type", (formInputSetting[db_name]["type"]) ? formInputSetting[db_name]["type"] : "text");
            Vue.set(input, "default_value", ((typeof formInputSetting[db_name]["default_value"] !== "undefined") ? formInputSetting[db_name]["default_value"] : null));
            input.label = (formInputSetting[db_name]["label"]) ? formInputSetting[db_name]["label"] : input.name;
            input.label_style = (formInputSetting[db_name]["label_style"]) ? formInputSetting[db_name]["label_style"] : {};

            input.input_style = {};
            input.input_style_dummy = formInputSetting[db_name]["input_style"] ? formInputSetting[db_name]["input_style"] : {};
            input.input_style_dummy.active_style = (input.input_style_dummy.active_style) ? input.input_style_dummy.active_style : {};
            input.input_style_dummy.style = (input.input_style_dummy.style) ? input.input_style_dummy.style : {};
            input.input_style.style = input.input_style_dummy.style;
            input.input_style.active_style = $.extend(input.input_style_dummy.active_style, input.input_style.style)
            // input.input_style.active_style = input.input_style.style(input.input_style.style);

            input.read_only = formInputSetting[db_name]["read_only"] ? formInputSetting[db_name]["read_only"] : false;
            input.value_changed = (formInputSetting[db_name]["value_changed"]) ? formInputSetting[db_name]["value_changed"] : null;
            Vue.set(input, "style", ((typeof formInputSetting[db_name]["style"] !== "undefined") ? formInputSetting[db_name]["style"] : null));
            let defaultPlaceholder = ""
            switch(input.type){
              case "number" :
                defaultPlaceholder = 0;
                break;
              case "decimal" :
                defaultPlaceholder = "0.00";
                break;
              default :
                defaultPlaceholder = input.name;
            }
            input.placeholder = (formInputSetting[db_name]["placeholder"]) ? formInputSetting[db_name]["placeholder"] : defaultPlaceholder;
            input.col = (formInputSetting[db_name]["col"]) ? formInputSetting[db_name]["col"]*1 : 12;
            input.col_label = (formInputSetting[db_name]["col_label"]) ? formInputSetting[db_name]["col_label"]*1 : (input.col === 6 ? 6 : 3);
            input.col_input ='col-sm-'+(12 - input.col_label);
            if(input.col_label === 12){
              input.label_style["text-align"] = "left";
              input.col_input ='col-sm-10 col-sm-offset-2';
            }
            if(input.type === "select"){
              input.select_option = formInputSetting[db_name]["select_option"];
              input.select_option.datetime_updated = false;
              Vue.set(input.select_option, "options", formInputSetting[db_name]["select_option"]["options"] ? formInputSetting[db_name]["select_option"]["options"] : {});
              if(typeof input.select_option.options === "undefined"){
                input.select_option.options = {};
              }

            }
            Vue.set(this.formValue, input.db_name, input.default_value);//set form value's default
            this.formValuePath[db_name] = currentPath.slice(0);
            settings[db_name] = input;
          }else{//form group
            settings[db_name] = formInputSetting[db_name];
            settings[db_name].col = (formInputSetting[db_name]["col"]) ? formInputSetting[db_name]["col"]*1 : 12;
            settings[db_name].group_name = formInputSetting[db_name]["group_name"] ? formInputSetting[db_name]["group_name"] : "";
            currentPath.push(db_name);
            Vue.set(settings[db_name], "input_setting", this.initFormInputSetting(formInputSetting[db_name]["input_setting"],currentPath))

          }
        }
        return settings;
      },
      underscoreToWord(string){
        return ((string).replace(/_/g, " ")).replace(/\w\S*/g, function(string){return string.charAt(0).toUpperCase() + string.substr(1).toLowerCase();})
      },
      dataFormat(value, inputSetting, self){
        let type = inputSetting["type"];
        let colValue = value;
        switch(type){
          case "decimal" :
            return ((colValue !==""  && colValue !== null && typeof colValue !== "undefined") ? colValue*1 : inputSetting["default_value"])*1;
          case "number":
            return (colValue !==""  && colValue !== null && typeof colValue !== "undefined") ? colValue*1 : inputSetting["default_value"];
          case "checkbox" :
            return value*1 ? 1 :0;
          case "checkbox_option" :

            return (value*1) ? 1 :0;
          case "select":
            return ( colValue !== null) ? colValue*1 : (inputSetting["default_value"]);
          default :
            return colValue;
        }
      }
    }

  }
</script>
<style>
  input[type='decimal']{
    text-align: right;
  },
  input[type='number']{
    text-align: right;
  }
  .align-right{
    text-align:right
  }
</style>
