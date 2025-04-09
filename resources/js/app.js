import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Define global dark mode data for Alpine.js
document.addEventListener('alpine:init', () => {
    Alpine.data('theme', () => ({
        darkMode: localStorage.getItem('darkMode') === 'true' || 
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            document.documentElement.classList.toggle('dark', this.darkMode);
        },
        init() {
            // Initialize dark mode on load
            document.documentElement.classList.toggle('dark', this.darkMode);
            
            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (!localStorage.getItem('darkMode')) {
                    this.darkMode = e.matches;
                    document.documentElement.classList.toggle('dark', this.darkMode);
                }
            });
        }
    }));
});

Alpine.start();

window.Vue = require('vue');

Vue.component('follow-button', require('./components/FollowButton.vue').default);

const app = new Vue({
    el: '#app',
});
