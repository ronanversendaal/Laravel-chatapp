<template>
    <ul class="chat">
        <li class="left clearfix" v-for="thread in threads">
            <div class="chat-body clearfix">
                <div class="header">
                    <strong class="primary-font">
                        {{ thread.subject }}
                    </strong>
                </div>
                <p>
                    <!-- Last message in thread -->
                    <!-- {{ thread.message }} -->
                </p>
            </div>
        </li>
    </ul>
</template>

<script>
  export default {
    props: ['threads']
  };
</script>