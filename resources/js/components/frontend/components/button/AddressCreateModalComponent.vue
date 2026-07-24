<template>
    <button @click="add" data-modal="#address-modal" type="button" class="text-xs capitalize font-medium flex items-center rounded-3xl py-1.5 px-3.5 gap-1 text-white bg-primary transition hover:bg-primary/90">
        <i class="lab lab-add-circle lab-font-size-13 text-white"></i>
        <span>{{ props.title }}</span>
    </button>
</template>

<script>
import appService from "../../../../services/appService";

export default {
    name: "AddressCreateModalComponent",
    props: ['props'],
    methods: {
        add: function () {
            appService.modalShow('.address-modal');
        }
    }
}
</script>
