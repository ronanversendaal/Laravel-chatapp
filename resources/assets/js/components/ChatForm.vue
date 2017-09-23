<template>
    <div class="input-group">
        <input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." v-model="newMessage" @keyup.enter="sendMessageToThread">

        <span class="input-group-btn">
            <button class="btn btn-primary btn-sm" id="btn-chat" @click="sendMessageToThread">
                Send
            </button>
        </span>
    </div>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                thread : {},
                newMessage: ''
            }
        },

        created(){

            this.eventHub.$on('switch-thread', thread => {
                this.setCurrentThread(thread);
            });
        },

        methods: {

            setCurrentThread(thread){
                this.thread = thread;
            },
            sendMessageToThread() {
                this.$emit('messagesent', {
                    thread : this.thread.id,
                    user: this.user,
                    message: this.newMessage
                });

                this.newMessage = ''
            }
        }    
    }
</script>