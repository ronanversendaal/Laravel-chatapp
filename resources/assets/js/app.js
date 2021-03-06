
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.use(require('vue-chat-scroll'))

Vue.component('thread-chats', require('./components/ThreadChats.vue'));
Vue.component('chat-messages', require('./components/ChatMessages.vue'));
Vue.component('chat-form', require('./components/ChatForm.vue'));
Vue.component('chat-bubble', require('./components/ChatBubble.vue'));

const eventHub = new Vue() // Single event hub for emitting data across components.

// Distribute to components using global mixin
Vue.mixin({
    data: function () {
        return {
            eventHub: eventHub
        }
    }
})

const app = new Vue({
    el: '#app',

    data: {
        messages: [],
        threads: []
    },

    methods: {
        addMessageToThread(message) {
            axios.post('/threads/messages', message);
        }
    }
});