<template>
<div class="message_write">
    <textarea class="form-control" placeholder="type a message" v-model="newMessage" @keyup="enterHandler"></textarea>
    <div class="clearfix"></div>
    <div class="chat_bottom">
        <a href="#" class="pull-left upload_btn"><i class="fa fa-cloud-upload" aria-hidden="true"></i>Add Files</a>
        <a href="#" class="pull-right btn btn-success"  @click="sendMessageToThread" >Send</a>
    </div>
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

            enterHandler(e){
                if (e.keyCode === 13 && !e.shiftKey) {
                    this.sendMessageToThread();
                }
            },

            setCurrentThread(thread){
                this.thread = thread;
            },
            sendMessageToThread() {
                // Checks if the first characters are not linebreaks.
                if(this.newMessage.match(/[0-9a-zA-Z]+$/gm)){
                    this.$emit('messagesent', {
                        thread : this.thread.id,
                        user: this.user,
                        message: this.newMessage
                    });
                    this.newMessage = '';   
                }
            }
        }    
    }
</script>