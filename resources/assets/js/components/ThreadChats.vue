<template>
    <ul class="chat">
        <li class="left clearfix" v-for="thread, index in threads">
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
    props: ['threads'],
    methods : {
        lastMessage(index){
            return this.threads[index].messages[this.threads[index].messages.length - 1].message;
        }
    }
  };
</script>