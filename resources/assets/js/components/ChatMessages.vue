<template>
    <ul class="list-unstyled">
        <li class="clearfix" :class="fromUser(message.user)" v-for="message in currentMessages">
            <span class="chat-img1">
                <div class="circle">
                    <div v-if="isUser(message.user)">
                        <div class="initials">{{getInitials(message.user)}}</div>
                    </div>
                    <div v-else>
                        <div class="initials">{{getInitials(message.thread)}}</div>
                    </div>
                </div>
            </span>
            <div class="chat-body1 clearfix">
                <p v-html="message.message"></p>
                <div class="chat_time">{{ message.created_at }}</div>
            </div>
        </li>
    </ul>
</template>

<script>
  export default {
    props: ['user', 'messages'],

    data() {
        return {
            currentMessages : [],
            currentThread : {}
        }
    },

    created() {
        this.eventHub.$on('messages', messages => {
            this.setCurrentMessages(messages);
        });
        this.eventHub.$on('message-add', e => {
            this.currentMessages.push({
              message: e.message.message,
              user: e.user,
              created_at : e.message.created_at
            });
        })
        this.eventHub.$on('switch-thread', thread => {
            this.currentThread = thread;
        });
    },
    methods : {

        fromUser : function (user){
            if(this.isUser(user)){ return 'admin_chat'; } 
            return 'client_chat';
        },

        isUser : function(user){
            if(user){ return true; }
            
            return false;
        },
        setCurrentMessages(messages){
            this.currentMessages = messages;
        },
        getInitials(thread){
            if(!thread){
                return "?";
            }
            var names = thread.name.split(' '),
                initials = names[0].substring(0, 1).toUpperCase();
            
            if (names.length > 1) {
                initials += names[names.length - 1].substring(0, 1).toUpperCase();
            }
            return initials;
        }
    }
  };
</script>