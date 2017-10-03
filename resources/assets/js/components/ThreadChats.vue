<template>
    <ul v-if="isVisible(display)" class="list-unstyled">
        <li @click="loadThread(thread)" v-for="thread, index in orderedThreads" class="left clearfix">

            <span class="chat-img1">
                <div class="circle">
                    <div class="initials">{{getUserInitials(thread)}}</div>
                </div>
            </span>
            <div class="chat-body clearfix">
                <div class="header_sec">
                    <strong class="primary-font">{{ thread.subject }}</strong> <strong class="pull-right">09:45AM</strong>
                </div>
                <div class="contact_sec">
                   <strong class="primary-font">{{messagePreview(lastMessage(index), 25)}}</strong>
                   <span class="badge pull-right">3</span>
                </div>
            </div>
        </li>
    </ul>
</template>

<script>
  export default {

    props : ['display', 'thread'],

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
        this.startRoom(this.thread);
    },
    methods : {
        messagePreview : function(message, limit){
            if(message.length > limit) {
                message = message.substring(0, (limit - 1))+"...";
            }
            return message;
        },
        isVisible(display){
            return display;
        },
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
        getUserInitials(thread){
            var names = thread.name.split(' '),
                initials = names[0].substring(0, 1).toUpperCase();
            
            if (names.length > 1) {
                initials += names[names.length - 1].substring(0, 1).toUpperCase();
            }
            return initials;
        },
        loadThread(thread){

            Echo.channel('chat.' + this.currentThread.chatroom).stopListening('MessageSentToThread');

            this.setCurrentThread(thread);
            this.getMessagesForThread(thread);

            // Pass the dynamic chatroom name here
            // @todo what if we cant connect? Show a message?
            Echo.channel('chat.' + this.currentThread.chatroom)
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
        startRoom(thread){

            if(thread){
                this.loadThread(thread);
                return;
            }

            this.getThreads().then(response => {
                this.setThreads(response.data).then(threads => {

                    // Get the last thread and load messages.
                    this.loadThread(this.threads[this.threads.length - 1]);
                });
            });
        },
        lastMessage(index){

            var message = this.orderedThreads[index].messages[this.orderedThreads[index].messages.length - 1];

            if(message){
                return message.message;                
            }
            return '-';

        }
    }
  };
</script>