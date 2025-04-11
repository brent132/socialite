import "./bootstrap";
import Alpine from "alpinejs";
import likeSystem from "./components/like-system";

// Register Alpine components
document.addEventListener("alpine:init", () => {
    Alpine.data("likeSystem", likeSystem);
});

window.Alpine = Alpine;
Alpine.start();

// Vue.js components
window.Vue = require("vue");
Vue.component(
    "follow-button",
    require("./components/FollowButton.vue").default
);

const app = new Vue({
    el: "#app",
});
