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
    var data = {};
    var tableConfig = {
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
          name : "Price",
          type : "number"
        },
        {
          name : "Stock on Hand",
          db_name : "stock",
          type : "number"
        },
        {
          name : "Reorder Level",
          type : "number"
        },
        {
          name : "Sell Indicator",
          db_name : "is_sell",
          type :"checkbox"
        },
        {
          name : "Purchase Indicator",
          db_name : "is_purchase",
          type :"checkbox"
        },
        {
          name : "Locked",
          type : "y/n"
        },
        {
          name : "Total Cost",
          number : "number"
        },
        {
          name : "Inactive",
          type : 'checkbox',
        },
        {
          name : "Category Code",
          db_name : "category.code"
        }
      ]
    };
    var formConfig = {
      formSetting :{
        title : "Product Detail"
      },
      formInputSetting : {
        locked :{
          type : "checkbox"
        },
        category_id : {
          label : "Category",
          type : "select",
          default_value : null,
          select_option : {
            option_function : function(formSetting, moduleInstance){
              moduleInstance.isLoading.push("category_select_option");
              var requestOption = {
                params : {
                  with_soft_delete : true
                }
              };
              var key = "category_id";
              if(formSetting[key]["select_option"]["datetime_updated"]){
                requestOption.params.condition = [
                  {
                    column : "updated_at",
                    clause : ">",
                    value : formSetting[key]["select_option"]["datetime_updated"]
                  }
                ]
              }
              moduleInstance.$http.get("api/category/retrieve", requestOption).then((response) => {
                var result = JSON.parse(response.body);
                if(result.data){
                  Vue.set(formSetting[key]["select_option"]["options"], 0,{
                    text : "Select",
                    value : null
                  });
                  for(var x in result.data){
                    Vue.set(formSetting[key]["select_option"]["options"], result.data[x]["id"], {
                      text : result.data[x]["description"],
                      value : result.data[x]["id"]
                    });
                    if(new Date(result.data[x]["updated_at"]) > new Date(formSetting[key]["select_option"]["datetime_updated"])){
                      formSetting[key]["select_option"]["datetime_updated"] = result.data[x]["updated_at"];
                    }
                  }
                }
                moduleInstance.isLoading.pop();
              });
            }
          }
        },
        code : {},
        description : {},
        unit_id : {
          label : "Unit",
          type : "select",
          default_value : null,
          select_option : {
            option_function : function(formSetting, moduleInstance){
              moduleInstance.isLoading.push("unit_select_option");
              var requestOption = {
                params : {
                  with_soft_delete : true
                }
              };
              var key = "unit_id";
              if(formSetting[key]["select_option"]["datetime_updated"]){
                requestOption.params.condition = [
                  {
                    column : "updated_at",
                    clause : ">",
                    value : formSetting[key]["select_option"]["datetime_updated"]
                  }
                ]
              }
              moduleInstance.$http.get("api/unit/retrieve", requestOption).then((response) => {
                var result = JSON.parse(response.body);
                Vue.set(formSetting[key]["select_option"]["options"], 0,{
                  text : "Select",
                  value : null
                });
                if(result.data){
                  for(var x in result.data){

                    Vue.set(formSetting[key]["select_option"]["options"], +result.data[x]["id"], {
                      text : result.data[x]["description"],
                      value : result.data[x]["id"]
                    });
                    if(new Date(result.data[x]["updated_at"]) > new Date(formSetting[key]["select_option"]["datetime_updated"])){
                      formSetting[key]["select_option"]["datetime_updated"] = result.data[x]["updated_at"];
                    }
                  }
                }
                moduleInstance.isLoading.pop();
              });
            }
          }
        },
        classification : {
          label : "Item Class",
          type : "select",
          default_value : 0,
          select_option : {
            options : [
              {value: 0, text : "Please Select"},
              {value: 1, text : "Activity"},
              {value: 2, text : "Assembly"},
              {value: 3, text : "Charge"},
              {value: 4, text : "Description-Only"},
              {value: 5, text : "Labor"},
              {value: 6, text : "Non-stock Item"},
              {value: 7, text : "Service"},
              {value: 8, text : "Stock Item"}
            ]
          },
          value_changed : function(inputValues, self){
            if(inputValues["classification"] === 6){
              Vue.set(self.formInputSetting.stock, "style", {visibility : "hidden"});
              Vue.set(self.formInputSetting.reorder_level, "style", {visibility : "hidden"});
              Vue.set(self.formInputSetting.cost, "style", {visibility : "hidden"});

              Vue.set(inputValues, "stock", 0);
              Vue.set(inputValues, "reorder_level", 0);
            }else{
              Vue.set(self.formInputSetting.stock, "style", {visibility : null});
              Vue.set(self.formInputSetting.reorder_level, "style", {visibility : null});
              Vue.set(self.formInputSetting.cost, "style", {visibility : null});

              Vue.set(inputValues, "stock",  null);
              Vue.set(inputValues, "reorder_level", null);
            }
          }
        },
        is_sell : {
          label : "I'll sell this item?",
          type : "checkbox",
          col : 6,
          col_label : 10
        },
        stock : {
          label : "Stock on Hand",
          type : "number",
          col : 6,
        },
        is_purchase : {
          label : "I'll purchase this item?",
          type : "checkbox",
          col : 6,
          col_label : 10
        },
        reorder_level : {
          type : "number",
          col : 6
        },
        is_commission : {
          label : "Commission Ind.",
          type : "checkbox",
          col : 6,
          col_label : 10
        },
        price : {
          type : "decimal",
          col : 6,
        },
        is_transferred : {
          label : "Transferred to Poultry Main",
          type : "checkbox",
          col : 6,
          col_label : 10
        },
        cost : {
          type : "decimal",
          col : 6,
        },
        inactive : {
          label : "Inactive?",
          type : "checkbox",
          col : 6,
          col_label : 10
        },
        dummy_1 : {
          col : 6
        },
        packing : {
          label : "Packing(kls)",
          type : "number",
          col : 6,
        },
        cost_method : {
          type : "select",
          default_value : 1,
          select_option : {
            options : [
              { value : 1 , text : "Average"},
              { value : 2 , text : "LIFO"}
            ]
          },
          input_style : {
            background : "#fff4b9"
          },
          col : 6
        },
        dosage : {
          type : "textarea",
          col : 6,
          input_style : {
            background : "#fff4b9",
            resize: "none"
          }
        },
        form_group_on_order : {

          border_style : "all",
          col : 6,
          inputSetting : {
            quantity_on_order : {
              type : "number",
              col_label: 12,
              placeholder : "0.00"
            },
            pending_customer_order : {
              type : "number",
              col_label : 12,
              placeholder : "0.00"
            }
          }
        }
      }
    };
    var moduleConfig = {
      api : "product",
      tableConfig : tableConfig,
      formConfig : formConfig
    }
    data.commonFormConfig = moduleConfig;
    return data;
  }
}
</script>
