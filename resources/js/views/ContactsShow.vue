<template>
    <div>
        <div v-if="loading">Loading...</div>
        <div v-else>
            <div class="flex justify-between">
                <a href="#" class="text-blue-400" @click="$router.back()">
                    < Back
                </a>
                <div class="relative">
                    <router-link :to="'/contacts/' + contact.contact_id + '/edit'"
                                 class="px-4 py-2 mr-2 rounded text-sm font-bold text-green-500 border border-green-500">
                        Edit
                    </router-link>
                    <a href="#" class="px-4 py-2 rounded text-red-500 text-sm font-bold border border-red-500"
                       @click="modal = !modal">Delete</a>

                    <div v-if="modal" class="absolute p-8 w-64 right-0 mt-3 bg-blue-900 text-white rounded-lg z-20">
                        <p>Are you sure you want to delete this record?</p>
                        <div class="flex items-center justify-end mt-6">
                            <button class="px-4 py-2 text-white bg-blue-800 rounded  text-sm font-bold mr-3"
                                    @click="modal = !modal">Cancel
                            </button>
                            <button class="px-4 py-2 text-white bg-red-500 rounded text-sm font-bold" @click="destroy">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="modal" class="bg-black opacity-25 absolute right-0 left-0 top-0 bottom-0 z-10" @click="modal = !modal">

                </div>
            </div>

            <div class="flex items-center pt-6">

                <UserCircle :name="contact.name"/>

                <p class="pl-5 text-xl">{{ contact.name }}</p>
            </div>
            <p class="pt-6 text-gray-600 font-bold uppercase text-sm">Contact</p>
            <p class="pt-2 text-blue-400">{{ contact.email }}</p>
            <p class="pt-6 text-gray-600 font-bold uppercase text-sm">Company</p>
            <p class="pt-2 text-blue-400">{{ contact.company }}</p>
            <p class="pt-6 text-gray-600 font-bold uppercase text-sm">Birhtday</p>
            <p class="pt-2 text-blue-400">{{ contact.birthday }}</p>
        </div>

    </div>
</template>

<script>
    import UserCircle from "../components/UserCircle";

    export default {
        name: "ContactsShow",
        components: {UserCircle},
        mounted() {
            axios.get('/api/contacts/' + this.$route.params.id)
                .then(response => {
                    this.contact = response.data.data;
                    this.loading = false;
                })
                .catch(errors => {
                    this.loading = false;
                    if (errors.response.status === 404) {
                        this.$router.push('/contacts');
                    }
                });
        },
        data: function () {
            return {
                loading: true,
                contact: null,
                modal: false,
            }
        },
        methods: {
            destroy: function () {
                axios.delete('/api/contacts/' + this.$route.params.id)
                    .then(response => {
                        this.$router.push('/contacts');
                    })
                    .catch(errors => {
                        alert('Internal Error. Unable to delete contact.')
                    });
            }
        }
    }
</script>

<style scoped>

</style>
