<template>
    <div class="w-full max-w-lg mx-auto px-4 py-8">
        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-8">
            <h3 class="text-lg font-black text-slate-900 mb-4">Example Component</h3>
            <p class="text-slate-600">I'm an example component.</p>
        </div>
    </div>
</template>
<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>