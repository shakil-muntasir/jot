<template>
    <div class="flex justify-center items-center rounded-full border border-gray-400 text-white bg-blue-400 w-10 h-10">
        {{ userCircle }}
    </div>
</template>

<script>
    export default {
        name: "UserCircle",
        props: [
            'name'
        ],
        computed: {
            userCircle: function () {
                return this.name.match(/\b(\w)/g).slice(0,2).join('').toUpperCase();
            }
        }
    }
</script>

<style scoped>

</style>
