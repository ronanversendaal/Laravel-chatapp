<template>
<ul class="list-unstyled">
    <li class="clearfix" :class="{  'admin_chat' :  fromCurrentUser(message.user) }" v-for="message in currentMessages">
        <div class="chat-body1 clearfix">
            <span>{{message.user.name}}</span>
            <p>{{ message.message }}</p>
            <div class="chat_time pull-right">09:40PM</div>
        </div>
    </li>        
</ul>
</template>

<script>
  export default {
    props: ['user', 'messages'],

    data() {
        return {
            currentMessages : []
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
    },
    methods : {
        setCurrentMessages(messages){
            this.currentMessages = messages;
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