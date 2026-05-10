import './bootstrap';
import { createApp, defineAsyncComponent } from 'vue';
import Alpine from 'alpinejs';
import { RecycleScroller, DynamicScroller } from 'vue-virtual-scroller';
import 'vue-virtual-scroller/dist/vue-virtual-scroller.css';

window.Alpine = Alpine;
Alpine.start();

const app = createApp({});
// Register the components globally
app.component('RecycleScroller', RecycleScroller);
app.component('DynamicScroller', DynamicScroller);
app.component('virtual-scroller', RecycleScroller); // Alias to match what was used in GrudgeManager


const components = {
    'example-component': () => import('./components/ExampleComponent.vue'),
    'teaching-manager': () => import('./components/TeachingManager.vue'),
    'grudge-manager': () => import('./components/GrudgeManager.vue'),
    'military-manager': () => import('./components/MilitaryManagerV2.vue'),
    'registry-manager': () => import('./components/RegistryManager.vue'),
    'mobile-dashboard': () => import('./components/MobileDashboard.vue'),
    'imperial-grace-manager': () => import('./components/ImperialGraceManager.vue'),
    'admin-dashboard': () => import('./components/AdminDashboard.vue'),
    'admin-root-selector': () => import('./components/AdminRootSelector.vue'),
    'search-component': () => import('./components/SearchComponent.vue'),
    'add-action-menu': () => import('./components/AddActionMenu.vue'),
    'imperial-grace-add-form': () => import('./components/ImperialGraceAddForm.vue'),
    'grudge-add-form': () => import('./components/GrudgeAddForm.vue'),
    'military-add-form': () => import('./components/MilitaryAddForm.vue'),
    'registry-add-form': () => import('./components/RegistryAddForm.vue'),
    'other-manager': () => import('./components/OtherManager.vue'),
    'trash-manager': () => import('./components/TrashManager.vue'),
    'kaiwen-manager': () => import('./components/KaiwenManager.vue'),
    'other-teaching-manager': () => import('./components/OtherTeachingManager.vue'),
    'other-records-manager': () => import('./components/OtherRecordsManager.vue'),
    'pagination-buttons': () => import('./components/PaginationButtons.vue'),
    'logo-imperial-notebook': () => import('./components/LogoImperialNotebook.vue'),
};

Object.entries(components).forEach(([name, loader]) => {
    app.component(name, defineAsyncComponent(loader));
});

app.mount('#app');
