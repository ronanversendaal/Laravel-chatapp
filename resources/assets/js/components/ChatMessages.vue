<template>
<ul class="list-unstyled">
    <li class="clearfix" :class="isUser(message.user)" v-for="message in currentMessages">
        <span class="chat-img1">
            <div class="circle">
                <div class="initials">{{getUserInitials(message.user)}}</div>
            </div>
        </span>
        <div class="chat-body1 clearfix">
            <p>{{ message.message }}</p>
            <div class="chat_time pull-right">{{ message.created_at }}</div>
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
              user: e.user
            });
        })
        this.eventHub.$on('switch-thread', thread => {
            this.currentThread = thread;
        });
    },
    methods : {

        isUser : function(user){
            if(user){
                return 'admin_chat';
            } else {
                return 'client_chat';
            }
        },
        setCurrentMessages(messages){
            this.currentMessages = messages;
        },
        getUserInitials(thread){
            console.log(thread);
            if(!thread){
                return "?";
            }
            var names = thread.name.split(' '),
                initials = names[0].substring(0, 1).toUpperCase();
            
            if (names.length > 1) {
                initials += names[names.length - 1].substring(0, 1).toUpperCase();
            }
            return initials;
        },
        fromCurrentUser: function (user) {
            if(!user){
                return false;
            }
            return user.id === this.user.id;
        }
    }
  };
</script>