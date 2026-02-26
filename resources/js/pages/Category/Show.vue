<script setup lang="ts">
import Pagination from '@/components/custom/Pagination.vue';
import EventCard from '@/components/event/EventCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const filterOptions = [
    {
        name: 'Upcoming',
        value: 'upcoming',
    },
    {
        name: 'Just Added',
        value: 'just_added',
    },
    {
        name: 'Alphabetical (A-Z)',
        value: 'alp',
    },
];

const props = defineProps<{
    category: {
        id: number;
        name: string;
        slug: string;
    };
    events: {
        data: Array<{
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
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters: {
        sort?: string;
    };
}>();

const handleSort = (event: Event) => {
    const selectedSort = (event.target as HTMLSelectElement).value;

    router.get(
        `/categories/${props.category.slug}`,
        {
            sort: selectedSort,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};
</script>

<template>
    <Head title="{{" props.category.name }} />
    <AppLayout>
        <div class="mx-auto max-w-6xl">
            <section class="mb-6">
                <h1 class="pb-5 text-3xl font-bold">
                    All events related to {{ props.category.name }}
                </h1>
                <div class="text-md flex flex-row gap-3 pb-5">
                    <p>Sort by:</p>
                    <select
                        @change="handleSort"
                        :value="props.filters.sort || 'upcoming'"
                        class="rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                    >
                        <option
                            v-for="filter in filterOptions"
                            :key="filter.value"
                            :value="filter.value"
                        >
                            {{ filter.name }}
                        </option>
                    </select>
                </div>
                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        :href="'/events/' + event.slug"
                        v-for="event in props.events.data"
                        :key="event.id"
                    >
                        <EventCard :event="event" />
                    </Link>
                </div>
                <div class="flex justify-center">
                    <Pagination :links="props.events.links" />
                </div>
            </section>
        </div>
    </AppLayout>
</template>
