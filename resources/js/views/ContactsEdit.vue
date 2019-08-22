<template>
    <div>
        <div class="flex justify-between">
            <a href="#" class="text-blue-400" @click="$router.back()">
                < Back
            </a>
        </div>

        <form @submit.prevent="submitForm" class="mt-4">
            <InputField name="name" label="Contact Name" :data="form.name" :errors="errors" placeholder="John Appleseed" @update:field="form.name = $event"/>
            <InputField name="email" label="E-mail" :data="form.email" :errors="errors" placeholder="your@email.com" @update:field="form.email = $event"/>
            <InputField name="company" label="Company" :data="form.company" :errors="errors" placeholder="Pineapple Inc." @update:field="form.company = $event"/>
            <InputField name="birthday" label="Birthday" :data="form.birthday" :errors="errors" placeholder="MM/DD/YYYY" @update:field="form.birthday = $event"/>

            <div class="flex justify-end">
                <button class="px-4 py-2 rounded border text-red-700 mr-5 hover:border-red-700" @click.prevent="$router.back()">Cancel</button>
                <button class="bg-blue-500 px-4 py-2 rounded text-white hover:bg-blue-400">Save Changes</button>
            </div>
        </form>
    </div>
</template>

<script>
    import InputField from "../components/InputField";
    export default {
        name: "ContactsEdit",
        components: {
            InputField,
        },
        mounted() {
            axios.get('/api/contacts/' + this.$route.params.id)
                .then(response => {
                    this.form = response.data.data;
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
                form: {
                    'name' : '',
                    'email' : '',
                    'company' : '',
                    'birthday' : '',
                },
                errors: null,
            }
        },
        methods: {
            submitForm: function () {
                axios.patch('/api/contacts/' + this.$route.params.id, this.form)
                    .then(response => {
                        this.$router.push(response.data.links.self);
                    })
                    .catch(errors => {
                        this.errors = errors.response.data.errors;
                    });
            }
        }
    }
</script>

<style scoped>

</style>
