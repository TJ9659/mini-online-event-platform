<script setup lang="ts">
import EventCard from '@/components/event/EventCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import * as LucideIcons from 'lucide-vue-next';
import { show } from '@/actions/App/Http/Controllers/CategoryController';

const props = defineProps<{
    upcomingEvents: Array<{
        id: number;
        title: string;
        slug: string;
        description: string;
        platform_name: string;
        cover_image: string;
        formatted_date: string;
        formatted_start_time: string;
        capacity: number;
    }>;
    featuredEvents: Array<{
        id: number;
        title: string;
        slug: string;
        description: string;
        cover_image: string;
        platform_name: string;
        formatted_date: string;
        formatted_start_time: string;
        capacity: number;
    }>;
    categories: Array<{
        id: number;
        name: string;
        slug: string;
        icon: string;
    }>;
}>();
</script>

<template>
    <Head title="Homepage" />
    <AppLayout>
        <div class="mx-auto max-w-6xl">
            <img
                src="/images/banner.png"
                alt="banner-img"
                class="mb-4 h-[200px] w-full rounded-xl object-cover md:h-[500px]"
                sizes="(max-width: 1920px) 100vw, 1920px"
                fetchpriority="high"
            />
            <section class="mb-6">
                <h2 class="mb-6 text-lg font-semibold text-gray-900">
                    Browse by Category
                </h2>
                <div
                    class="grid grid-cols-3 justify-items-center gap-6 pb-5 md:grid-cols-4 lg:grid-cols-6"
                >
                    <Link
                        :href="show(category)"
                        v-for="category in categories"
                        :key="category.id"
                        class="group flex flex-col items-center"
                    >
                        <div
                            class="flex aspect-square md:w-24 w-16  items-center justify-center rounded-full border border-gray-200 transition-all group-hover:border-indigo-200 group-hover:bg-indigo-50"
                        >
                            <component
                                :is="(LucideIcons as any)[category.icon]"
                                :size="32"
                                stroke-width="1.5"
                                class="text-gray-700 group-hover:text-indigo-600"
                            />
                        </div>

                        <span
                            class="mt-3 text-sm font-medium text-gray-600 group-hover:text-indigo-600"
                        >
                            {{ category.name }}
                        </span>
                    </Link>
                </div>
            </section>

            <section class="mb-6">
                <h1 class="pb-5 text-3xl font-bold">Featured Events</h1>
                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        :href="'/events/' + event.slug"
                        v-for="event in featuredEvents"
                        :key="event.id"
                    >
                        <EventCard :event="event" />
                    </Link>
                </div>
            </section>

            <section class="mb-6">
                <h1 class="pb-5 text-3xl font-bold">Upcoming Events</h1>
                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        :href="'/events/' + event.slug"
                        v-for="event in upcomingEvents"
                        :key="event.id"
                    >
                        <EventCard :event="event" />
                    </Link>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
