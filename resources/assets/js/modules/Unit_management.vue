<template>
  <common-module-component :config="commonFormConfig" ></common-module-component>
</template>

<script>
export default{
  mounted(){
  },
  components :{
    "common-module-component" : require('../components/common_module_component/Common_module_component.vue'),
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
      columnSetting : [
        {
          name : "Code"
        },
        {
          name : "Description"
        },
        {
          name : "Multiplier",
          type : "number"
        }
      ],
      with_timestamp : true
    };
    let formConfig = {
      formSetting :{
        title : "Unit Detail"
      },
      formInputSetting : {
        code : {},
        description : {
          name : "Description"
        },
        multiplier : {
          name : "Multiplier",
          default_value : 1,
          type : "number"
        }
      }
    };
    let unitConfig = {
      api : "unit",
      tableConfig : tableConfig,
      formConfig : formConfig
    }
    data.commonFormConfig = unitConfig;
    return data;
  }
}
</script>
