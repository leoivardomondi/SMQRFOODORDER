<template>
    <div style="display: none;"></div>
</template>

<script>
import VueElementLoading from 'vue-element-loading';

export default {
    name: "LoadingComponent",
    components: {VueElementLoading},
    props: ['props'],
    data() {
        return {
            isActive: false
        }
    }
}
</script>
