<template>
    <div v-show="isTyping" class="chat-bubble clearfix">
        <p class="typing">&middot; &middot; &middot;</p>
    </div>
</template>

<script>
export default {

    data() {
        return {
            isTyping : false,
            currentMessages : [],
            currentThread : {}
        }
    },
    created(){
        this.eventHub.$on('chat-action', action => {
            switch(action.action){
                case 'typing':
                    this.isTyping = true;
                    break;
                case 'stop_typing':
                    this.isTyping = false;
                    break;
            }
        });
    }
}    
</script>
