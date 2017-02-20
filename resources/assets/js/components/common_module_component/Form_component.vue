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
        <button v-on:click="$emit('change-entry', 0)" type="button" class="btn btn-default"><i class="fa fa-step-backward" aria-hidden="true"></i></button>
        <button v-on:click="$emit('change-entry', 1)" type="button" class="btn btn-default"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
        <button v-on:click="$emit('change-entry', 2)" type="button" class="btn btn-default"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
        <button v-on:click="$emit('change-entry', 3)" type="button" class="btn btn-default"><i class="fa fa-step-forward" aria-hidden="true"></i></button>
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
              <div class="row">
                <div class="col-sm-12" v-if="!readOnly">
                  <button v-on:click="submitForm" type="button" v-bind:disabled="isLoading.length ? true : false">{{this.entry_id > 0 ? "Save" : "Create"}}</button>
                  <button v-on:click="cancelForm" type="button" v-bind:disabled="isLoading.length ? true : false">{{this.entry_id > 0 ? "Close" : "Cancel"}}</button>
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
        if(value === -1){//create
          for(var key in this.formValue){
            if(key !== "id"){
              this.updateFormValue(key, null);
              var setting = this.formInputSetting;
              for(var pathIndex in this.formValuePath[key]){
                setting = setting[this.formValuePath[key][pathIndex]]["inputSetting"];
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
        var setting = this.formInputSetting
        for(var pathIndex in this.formValuePath[name]){
          setting = setting[this.formValuePath[name][pathIndex]]["inputSetting"];
        }
        var formattedValue = this.dataFormat(value, setting[name]);
        Vue.set(this.formValue, name, formattedValue);

        if(setting[name]["value_changed"]){
          setting[name]["value_changed"](this.formValue, this);
        }
      },
      deleteEntry(){
        this.isLoading.push("deleteEntry");
        var requestOption = {
          params : {
            "id" : this.entry_id
          }
        };
        this.$http.get(this.api.delete, requestOption).then((response) => {
          // success callback
          var result = JSON.parse(response.body);
          if(result.data){
            this.$emit("entry-deleted", this.entry_id);
            this.entry_id=-1;
            this.cancelForm();
          }else{
          }
          this.isLoading.pop();
        });
      },
      showEntry(){
        this.isLoading.push("showEntry");
        if(this.showEntryRequest){

          this.showEntryRequest.abort();
        }
        var requestOption = {
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
          var result = JSON.parse(response.body);
          if(result.data){
            for(var key in this.formValue){
              if(key !== "id" && result.data[key]){
                this.updateFormValue(key, result.data[key]);
                var setting = this.formInputSetting;
                for(var pathIndex in this.formValuePath[key]){
                  setting = setting[this.formValuePath[key][pathIndex]]["inputSetting"];
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

        for(var x in this.formValue){
          if(x !== "id"){
            this.updateFormValue(x, null);
          }
        }
        this.updatedAtTimestamp = "";
      },
      submitForm(){
        this.isLoading.push("submitForm");
        var requestOption = {
          params : this.formValue
        };
        var link = this.api.create;
        if(this.entry_id > 0){
          requestOption.params.id = this.entry_id;
          link = this.api.update;
        }
        this.$http.get(link, requestOption).then((response) => {
          // success callback
          var result = JSON.parse(response.body);
          if(result.data){
            if(this.entry_id === -1){
              this.entry_id = result.data;
              this.$emit("entry-created", this.entry_id);
            }else{
              this.$emit("entry-updated", this.entry_id);
            }
            this.updatedAtTimestamp = result.request_timestamp;
          }else{
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
        var currentPath = path.slice(0);
        var settings = {};
        for(var db_name in formInputSetting){
          var input = {};
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
            input.input_style = (formInputSetting[db_name]["input_style"]) ? formInputSetting[db_name]["input_style"] : {};
            input.value_changed = (formInputSetting[db_name]["value_changed"]) ? formInputSetting[db_name]["value_changed"] : null;
            Vue.set(input, "style", ((typeof formInputSetting[db_name]["style"] !== "undefined") ? formInputSetting[db_name]["style"] : null));
            var defaultPlaceholder = ""
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
            Vue.set(settings[db_name], "inputSetting", this.initFormInputSetting(formInputSetting[db_name]["inputSetting"],currentPath))

          }
        }
        return settings;
      },
      underscoreToWord(string){
        return ((string).replace(/_/g, " ")).replace(/\w\S*/g, function(string){return string.charAt(0).toUpperCase() + string.substr(1).toLowerCase();})
      },
      dataFormat(value, inputSetting, self){
        var type = inputSetting["type"];
        var colValue = value;
        switch(type){
          case "decimal" :
            return ((colValue !==""  && colValue !== null && typeof colValue !== "undefined") ? colValue*1 : inputSetting["default_value"])*1;
          case "number":
            return (colValue !==""  && colValue !== null && typeof colValue !== "undefined") ? colValue*1 : inputSetting["default_value"];
          case "checkbox" :
            return value*1 ? 1 :0;
          case "checkbox_option" :
            return value*1 ? 1 :0;
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
