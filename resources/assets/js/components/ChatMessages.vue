<template>
    <ul class="chat">
        <li class="left clearfix" v-for="message in currentMessages">
            <div class="chat-body clearfix">
                <div class="header">
                    <strong class="primary-font">
                        {{ message.user.name }}
                    </strong>
                </div>
                <p>
                    {{ message.message }}
                </p>
            </div>
        </li>
    </ul>
</template>

<script>
  export default {

    data() {
        return {
            currentMessages : []
        }
    },

    props: ['messages'],

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
    }
  };
</script>