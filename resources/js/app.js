/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

import TeachingManager from './components/TeachingManager.vue';
app.component('teaching-manager', TeachingManager);

import GrudgeManager from './components/GrudgeManager.vue';
app.component('grudge-manager', GrudgeManager);

import MilitaryManager from './components/MilitaryManager.vue';
app.component('military-manager', MilitaryManager);

import RegistryManager from './components/RegistryManager.vue';
app.component('registry-manager', RegistryManager);

import MobileDashboard from './components/MobileDashboard.vue';
app.component('mobile-dashboard', MobileDashboard);

import ImperialGraceManager from './components/ImperialGraceManager.vue';
app.component('imperial-grace-manager', ImperialGraceManager);

import AdminDashboard from './components/AdminDashboard.vue';
app.component('admin-dashboard', AdminDashboard);

import AdminRootSelector from './components/AdminRootSelector.vue';
app.component('admin-root-selector', AdminRootSelector);

import SearchComponent from './components/SearchComponent.vue';
app.component('search-component', SearchComponent);

import AddActionMenu from './components/AddActionMenu.vue';
app.component('add-action-menu', AddActionMenu);

import ImperialGraceAddForm from './components/ImperialGraceAddForm.vue';
app.component('imperial-grace-add-form', ImperialGraceAddForm);

import GrudgeAddForm from './components/GrudgeAddForm.vue';
app.component('grudge-add-form', GrudgeAddForm);

import MilitaryAddForm from './components/MilitaryAddForm.vue';
app.component('military-add-form', MilitaryAddForm);

import RegistryAddForm from './components/RegistryAddForm.vue';
app.component('registry-add-form', RegistryAddForm);

import OtherManager from './components/OtherManager.vue';
app.component('other-manager', OtherManager);

import TrashManager from './components/TrashManager.vue';
app.component('trash-manager', TrashManager);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
