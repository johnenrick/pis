<template >
  <div class="row" >
    <div v-if="tableConfig" v-bind:class="'col-sm-'+(formConfig ? 8 : 12)">
      <table-component
        ref="tableComponent"
        v-on:row-clicked="viewEntry"
        v-on:table-updated="tableUpdated"
        :api="api" :column_setting="tableConfig.columnSetting"
        :filter_setting="tableConfig.filterSetting"
        :current_row="tableConfig.currentRow"
        :timestamp="tableConfig.timestamp"
        ></table-component>

    </div>
    <div v-if="formConfig" v-bind:class="'col-sm-'+(tableConfig ? 4 : 12)">
      <form-component v-on:entry-updated="entryUpdated" v-on:entry-created="entryCreated" v-on:change-entry="changeEntry" v-on:entry-deleted="entryDeleted"  :api="api" :form_setting="formConfig.formSetting" :form_input_setting="formConfig.formInputSetting" :id="formConfig.id" :read_only="formConfig.readOnly"></form-component>
    </div>
  </div>
</template>
<script>
/**
var tableConfig = {
  apiSetting : {
    link : "product/retrieve"
  },
  filterSetting : [
    {
      name : "Product Description",
      db_name : "Product Description",
      placeholder : "Product Description",
      default : "asd",
      type : "text",
    },
    {
      name : "Product Type",
      default : "0",
      type : "select",
      select_option : [{
          value : 0,
          description : "None"
        },{
          value : 1,
          description : "Plastic"
        },{
          value : 2,
          description : "Glass"
        }]
    }
  ],
  columnSetting : [[
    {
      name : "Image",
      db_name: "image_link",
      type : "image",
      directory : "./public/img/"
    },
    {
      name : "Product Code"
    },{
      name : "Description",
      db_name : "description"
    },{
      name : "price",
      db_name : "price",
      type: "decimal"
    },{
      name : "Stock on Hand",
      rowspan : 1,
      colspan : 2,
    },{
      name : "Reorder Level",
      db_name : "reorder_level",
      type : "number"
    },
  ],[
    {
      name:  "In",
      db_name : "out_quantity",
      type: "number",
    },
    {
      name:  "Out",
      db_name : "in_quantity",
      type : "number"
    }
  ]]
};
var formConfig = {
  formSetting :{
    api : "Product",
    title : "Product Detail"
  },
  formInput : [
    {
      name : "Description",
      placeholder : "Enter Description"

    },
    {
      name : "Product Code",
      col : 6
    },
    {
      name : "Price",
      type : "decimal",
      col : 6,
      col_label : 3
    }
  ]
};
var setting = {
  tableConfig : tableConfig,
  formConfig : formConfig
}
*/
  export default{
    mounted(){
    },
    components :{
      "table-component" : require('./Table_component.vue'),
      "form-component" : require('./Form_component.vue')
    },
    data(){
      var configuration = this.config;
      if(typeof configuration.api === "string"){
        configuration.api = {
          create : "api/"+configuration.api+"/create",
          retrieve : "api/"+configuration.api+"/retrieve",
          update : "api/"+configuration.api+"/update",
          delete : "api/"+configuration.api+"/delete",
        }
      }
      if(!configuration.tableConfig){
        configuration.tableConfig = false;
      }
      if(!configuration.formConfig){
        configuration.formConfig = false;
      }else{
        Vue.set(configuration.formConfig, "id", 0);
      }
      Vue.set(configuration.tableConfig, "currentRow", -1);
      Vue.set(configuration.tableConfig, "currentRowValue", -1);
      Vue.set(configuration.formConfig, "readOnly", true);
      configuration.tableConfig.totalRow = 0;
      return configuration;

    },
    props : {
      config : Object
    },
    methods : {
      entryDeleted(entryID){
        this.$refs.tableComponent.deleteRow(this.tableConfig.currentRowValue);
      },
      entryUpdated(entryID){
        this.$refs.tableComponent.updateRow(this.tableConfig.currentRowValue, entryID*1);
      },
      entryCreated(entryID){
        this.$refs.tableComponent.createRow(entryID);
      },
      tableUpdated(totalRow){
        this.tableConfig.totalRow = totalRow;
      },
      changeEntry(direction){
        if(this.tableConfig.currentRowValue === -1){
          this.tableConfig.currentRow = 0;
          if(direction*1 === 3){//last
            this.tableConfig.currentRow = this.tableConfig.totalRow-1;
          }
        }else{
          switch(direction*1){
            case 0 : //first
              this.tableConfig.currentRow = 0;
              break;
            case 1 ://previous
              if(this.tableConfig.currentRowValue){
                this.tableConfig.currentRow = this.tableConfig.currentRowValue - 1;
              }
              break;
            case 2://next
              if(this.tableConfig.currentRowValue < this.tableConfig.totalRow-1){
                this.tableConfig.currentRow = this.tableConfig.currentRowValue + 1;
              }
              break;
            case 3://last
              this.tableConfig.currentRow = this.tableConfig.totalRow-1;
              break;
          }
        }
      },
      viewEntry(id, row){
        this.tableConfig.currentRowValue = row;
        this.formConfig.id = 0;
        this.formConfig.readOnly = false;
        var vueInstance = this;
        setTimeout(function(){
          vueInstance.formConfig.id = id;
          vueInstance.formConfig.readOnly = true;
        }, 10);
      }
    }
  }
</script>
