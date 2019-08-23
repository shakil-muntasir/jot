import Vue from 'vue';
import VueRouter from 'vue-router';
import Index from "./views/Index";
import ContactsCreate from "./views/ContactsCreate";
import ContactsShow from "./views/ContactsShow";
import ContactsEdit from "./views/ContactsEdit";
import ContactsIndex from "./views/ContactsIndex";
import BirthdaysIndex from "./views/BirthdaysIndex";

Vue.use(VueRouter);

export default new VueRouter({
    routes: [
        {path: '/', component:Index},
        {path: '/contacts', component:ContactsIndex},
        {path: '/contacts/create', component:ContactsCreate},
        {path: '/contacts/:id', component:ContactsShow},
        {path: '/contacts/:id/edit', component:ContactsEdit},

        {path: '/birthdays', component:BirthdaysIndex},
    ],
    mode: 'history',
});
