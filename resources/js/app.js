/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");

import ElementUI from "element-ui";
import locale from "element-ui/lib/locale/lang/en";
import router from "./router";
import store from "./store";
import App from "./App.vue";

Vue.use(ElementUI, { locale });

Vue.filter("readableDateTime", function (v) {
  return v ? moment(v).format("DD-MMM-YYYY HH:mm") : "";
});

Vue.filter("readableDate", function (v) {
  return v ? moment(v).format("DD-MMM-YYYY") : "";
});

Vue.filter("readableDateShort", function (v) {
  return v ? moment(v).format("DD/MM/YY") : "";
});

Vue.filter("formatNumber", function (v) {
  // return v;
  try {
    v += "";
    var x = v.split(".");
    var x1 = x[0];
    var x2 = x.length > 1 ? "." + x[1] : "";
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
      x1 = x1.replace(rgx, "$1" + "," + "$2");
    }
    return x1 + x2;
  } catch (error) {
    return 0;
  }
});

import AppLayout from "./layouts/AppLayout";
import SimpleLayout from "./layouts/SimpleLayout";

Vue.component("app-layout", AppLayout);
Vue.component("simple-layout", SimpleLayout);

const app = new Vue({
  el: "#app",
  store,
  router,
  render: (h) => h(App),
});
