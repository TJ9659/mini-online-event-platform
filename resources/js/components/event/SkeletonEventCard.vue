<script setup lang="ts">
import Skeleton from '../ui/skeleton/Skeleton.vue';
</script>

<template>
    <div class="rounded-lg border border-gray-200 bg-white">
        <Skeleton class="h-[180px] w-full rounded-t-lg" />

        <div class="p-6">
            <Skeleton class="h-7 w-3/4" />

            <div class="mt-4 flex items-center justify-between">
                <Skeleton class="h-4 w-1/2" />
            </div>

            <div class="mt-4 space-y-2">
                <Skeleton class="h-4 w-full" />
                <Skeleton class="h-4 w-5/6" />
            </div>

            <Skeleton class="mt-4 h-4 w-1/3" />
        </div>
    </div>
</template>
