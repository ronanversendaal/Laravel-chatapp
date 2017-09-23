
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.component('thread-chats', require('./components/ThreadChats.vue'));
Vue.component('chat-messages', require('./components/ChatMessages.vue'));
Vue.component('chat-form', require('./components/ChatForm.vue'));

const eventHub = new Vue() // Single event hub

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

    created() {
        // Move this to the ThreadChatsvue for single 
        // this.getMessages();

        // this.getThreads();

        // Echo.private('chat')
        //   .listen('MessageSent', (e) => {
        //     this.messages.push({
        //       message: e.message.message,
        //       user: e.user
        //     });
        //   });
    },

    methods: {
        // getMessages() {
        //     axios.get('/messages').then(response => {
        //         this.messages = response.data;
        //     });
        // },

        // getThreads() {
        //     axios.get('/threads/messages').then(response => {
        //         this.threads = response.data;
        //     });
        // },

        // addMessage(message) {
        //     this.messages.push(message);

        //     axios.post('/messages', message).then(response => {
        //       console.log(response.data);
        //     });
        // },
        addMessageToThread(message) {
            this.messages.push(message);

            axios.post('/threads/messages', message).then(response => {
              console.log(response.data);
            });
        }
    }
});