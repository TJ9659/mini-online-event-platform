<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
defineProps<{
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>
}>();
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap mt-8">
        <template v-for="(link, key) in links" :key="key">
            
            <div
                v-if="link.url === null"
                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded-full cursor-not-allowed"
                v-html="link.label"
            />

            <Link
                v-else
                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded-full hover:bg-gray-300 focus:border-orange-500 focus:text-orange-500 transition-colors"
                :class="{ 'bg-orange-500 text-white border-orange-500': link.active }"
                :href="link.url"
                v-html="link.label"
            />
            
        </template>
    </div>
</template>