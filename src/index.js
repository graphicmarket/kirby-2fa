import LoginScreen from "./components/LoginScreen.vue";
import TfaField from "./components/TfaField.vue";

panel.plugin("graphicmarket/kirby-2fa", {
  login: LoginScreen,
  fields: {
    '2fa': TfaField
  },
});