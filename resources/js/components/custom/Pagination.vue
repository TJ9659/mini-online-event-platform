<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
defineProps<{
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}>();
</script>

<template>
    <div v-if="links.length > 3" class="mt-8 flex flex-wrap">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="mr-1 mb-1 cursor-not-allowed rounded-full border px-4 py-3 text-sm leading-4 text-gray-400"
                v-html="link.label"
            />

            <Link
                v-else
                class="mr-1 mb-1 rounded-full border px-4 py-3 text-sm leading-4 transition-colors hover:bg-gray-300 focus:border-orange-500 focus:text-orange-500"
                :class="{
                    'border-orange-500 bg-orange-500 text-white': link.active,
                }"
                :href="link.url"
                v-html="link.label"
            />
        </template>
    </div>
</template>
