<script setup lang="ts">
defineProps<{
event: {
       id: number;
        title: string;
        slug: string;
        description: string;
        platform_name: string;
        cover_image: string;
        formatted_date: string;
        formatted_start_time: string;
        capacity: number;
    }
}>();
</script>

<template>
    <div class="rounded-lg border border-gray-200 bg-white hover:shadow">
        <img
            :src="'/storage/' + event.cover_image"
            class="h-[180px] w-full object-cover"
        />
        <div class="p-6">
            <h3 class="text-xl font-bold">
                {{ event.title }}
            </h3>
            <div class="mt-2 flex items-center justify-between">
                <span class="text-sm font-bold">
                    {{ event.formatted_date }},
                    {{ event.formatted_start_time }}
                </span>
            </div>
            <p class="mt-4 line-clamp-2 text-gray-600">
                {{ event.description }}
            </p>

            <p class="mt-2 line-clamp-2 text-gray-600">
                Platform: {{ event.platform_name }}
            </p>
        </div>
    </div>
</template>
