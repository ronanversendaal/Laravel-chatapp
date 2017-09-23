<template>
    <ul class="list-unstyled">
        <li @click="loadThread(thread)" v-for="thread, index in orderedThreads" class="left clearfix">
            <div class="chat-body clearfix">
                <div class="header_sec">
                   <strong class="primary-font">{{ thread.subject }}</strong> <strong class="pull-right">
                   09:45AM</strong>
                </div>
                <div class="contact_sec">
                   <strong class="primary-font">{{lastMessage(index)}}</strong>
                   <span class="badge pull-right">3</span>
                </div>
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

    computed : {
        orderedThreads : function(){
            return _.orderBy(this.threads, 'updated_at', 'desc');
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
            this.eventHub.$emit('switch-thread', thread);
        },
        setCurrentMessages(messages){
            this.eventHub.$emit('messages', messages);
        },
        loadThread(thread){
            this.setCurrentThread(thread);
            this.getMessagesForThread(thread);
            
            // Pass the dynamic chatroom name here
            // @todo what if we cant connect? Show a message?
            Echo.private('chat.' + this.currentThread.chatroom)
                .listen('MessageSentToThread', (e) => {
                    this.eventHub.$emit('message-add', e);
                });
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
                    this.loadThread(this.threads[this.threads.length - 1]);
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