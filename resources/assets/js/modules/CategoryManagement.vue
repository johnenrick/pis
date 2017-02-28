<template>
  <common-module-component :config="commonFormConfig" ></common-module-component>
</template>

<script>
export default{
  mounted(){
  },
  components :{
    "common-module-component" : require('../components/common_module_component/CommonModuleComponent.vue'),
  },
  data(){
    let data = {};
    let tableConfig = {
      filterSetting : [
        {
          name : "Code"
        },
        {
          name : "Description"
        }

      ],
      columnSetting : [[
        {
          name : "Code"
        },
        {
          name : "Description"
        },
        {

          name : "Expense Mapping",
          rowspan : 1,
          colspan : 2
        }
      ],[
        {
          name : "Purchases",
          type : "checkbox",
          value_function : function(row){
            return row["expense_mapping"]&1 ? 1 : 0;
          }
        },{
          name : "Withdrawals",
          type : "checkbox",
          value_function : function(row){
            return (row["expense_mapping"]&2) ? 1 : 0;
          }
        }
      ]],
      with_timestamp : true
    };
    let formConfig = {
      formSetting :{
        title : "Category Detail"
      },
      formInputSetting : {
        code :{},
        description : {},
        monitor_budget : {
          label : "Monitor Budget ?",
          type : "checkbox",
          label_style : {
            color : "red"
          },
          col_label : 4
        },
        form_group_budget_mapping : {
          border_style : "all",
          group_name : "Budget Mapping",
          input_setting : {
            expense_mapping : {
              type : "hidden",
              value_changed : function(inputValues){
                inputValues["purchase"] = inputValues["expense_mapping"]&1 ? 1 : 0;
                inputValues["withdrawal"] = (inputValues["expense_mapping"]&2) ? 1 : 0;
              }
            },
            purchase : {
              type : "checkbox_option",
              label : "Map PURCHASES direct to Expense Account",
              label_style : {
                "background-color" : "orange"
              },
              value_changed : function(inputValues, moduleInstance){
                inputValues["expense_mapping"] = parseInt( (inputValues["withdrawal"])+""+(inputValues["purchase"]), 2);
                moduleInstance.$emit("input-changed", "expense_mapping", inputValues["expense_mapping"]);
              }

            },
            withdrawal : {
              type : "checkbox_option",
              label : "Map WITHDRAWALS direct to Expense Account",
              label_style : {
                "background-color" : "skyblue"
              },
              value_changed : function(inputValues, moduleInstance){
                inputValues["expense_mapping"] = parseInt( (inputValues["withdrawal"])+""+(inputValues["purchase"]), 2);
                moduleInstance.$emit("input-changed", "expense_mapping", inputValues["expense_mapping"]);
              }
            }
          }
        }
      }
    };
    let unitConfig = {
      api : "category",
      tableConfig : tableConfig,
      formConfig : formConfig
    }
    data.commonFormConfig = unitConfig;
    return data;
  }
}
</script>
