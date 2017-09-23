<template>
    <ul class="chat">
        <li @click="loadThread(thread)" class="left clearfix" v-for="thread, index in threads">
            <div class="chat-body clearfix">
                <div class="header">
                    <strong class="primary-font">
                        {{ thread.subject }}
                    </strong>
                </div>
                <p>
                    {{lastMessage(index)}}
                </p>
            </div>
        </li>
    </ul>
</template>

<script>
  export default {

    data () {
        return {
            threads : [],
            currentThread : {}
        }
    },

    created() {
        this.startRoom();
    },
    methods : {
        getMessagesForThread(thread) {
            axios.get('/threads/'+thread.id+'/messages').then(response => {
                this.setCurrentMessages(response.data);
            });
        },
        setCurrentThread(thread){
            this.currentThread = thread;
        },
        setCurrentMessages(messages){
            this.eventHub.$emit('messages', messages);
        },
        loadThread(thread){
            this.setCurrentThread(thread);
            this.getMessagesForThread(thread);
        },
        getThreads() {
            return axios.get('/threads/messages');
        },
        setThreads (threads) {

            var threads = threads;

            this.threads = threads;

            return new Promise(function(resolve, reject){
                if(threads){
                    resolve(threads);    
                }
                reject();
            })
        },
        startRoom(){
            this.getThreads().then(response => {
                this.setThreads(response.data).then(threads => {

                    // Get the last thread and load messages.
                    this.setCurrentThread(this.threads[this.threads.length - 1]);
                    this.getMessagesForThread(this.currentThread);

                    // Pass the dynamic chatroom name here
                    // @todo enter thread information to message
                    // @todo what if we cant connect? Show a message?
                    Echo.private('chat.' + this.currentThread.chatroom)
                        .listen('MessageSent', (e) => {
                            this.messages.push({
                              message: e.message.message,
                              user: e.user
                            });
                        });
                });
            });
        },
        lastMessage(index){

            var message = this.threads[index].messages[this.threads[index].messages.length - 1];

            if(message){
                return message.message;                
            }
            return '-';

        }
    }
  };
</script>