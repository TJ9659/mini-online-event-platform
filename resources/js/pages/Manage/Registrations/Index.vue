<script setup lang="ts">
import AttendeesListModal from '@/components/custom/AttendeesListModal.vue';
import Pagination from '@/components/custom/Pagination.vue';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Eye, Search } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    events: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            platform_name: string;
            formatted_date: string;
            formatted_start_time: string;
            capacity: number;
            status: string;
            tickets_count: number;
            tickets: Array<{
                id: number;
                user: {
                    name: string;
                    email: string;
                };
                status: string;
            }>;
        }>;
        links: any;
    };
    filters: { search?: string };
}>();
const searchQuery = ref(props.filters.search || '');

const selectedEvent = ref<any>(null);
const isModalOpen = ref(false);

const handleSearch = () => {
    router.get(
        '/manage/events',
        { search: searchQuery.value },
        { preserveState: true },
    );
};

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Registrations',
        href: '/manage/registrations',
    },
];

const openRegistrationsModal = (event: any) => {
    selectedEvent.value = event;
    isModalOpen.value = true;
};

const closeModal = () => {
    selectedEvent.value = null;
    isModalOpen.value = false;
};
</script>

<template>
    <Head title="Manage Events" />

    <DashboardLayout :breadcrumbs="breadcrumbItems">
        <div class="min-w-0 flex-1">
            <div class="mx-auto max-w-6xl p-4 sm:p-8">
                <div
                    class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <h1 class="text-2xl font-bold text-gray-900">
                        Registrations for All Events
                    </h1>

                    <div
                        class="flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1 focus-within:ring-2 focus-within:ring-orange-500"
                    >
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search your events..."
                            class="w-full bg-transparent text-sm outline-none sm:w-64"
                            @keydown.enter="handleSearch"
                        />
                        <button
                            @click="handleSearch"
                            class="p-1 hover:text-orange-500"
                        >
                            <Search class="size-4" />
                        </button>
                    </div>
                </div>
                <div v-if="events.data.length === 0">
                    <p class="text-lg text-gray-500">
                        You have not created an event, create one to view
                        registrations!
                    </p>
                </div>

                <div
                    v-else
                    class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm"
                >
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase"
                                >
                                    Event Details
                                </th>
                                <th
                                    class="hidden px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase sm:table-cell"
                                >
                                    Date & Time
                                </th>
                                <th
                                    class="hidden px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase lg:table-cell"
                                >
                                    Platform
                                </th>
                                <th
                                    class="hidden px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase lg:table-cell"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold tracking-wider text-gray-500 uppercase"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="event in events.data"
                                :key="event.id"
                                class="transition-colors hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ event.title }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-500 sm:hidden"
                                    >
                                        {{ event.formatted_date }}
                                    </div>
                                </td>

                                <td
                                    class="hidden px-6 py-4 whitespace-nowrap sm:table-cell"
                                >
                                    <div class="text-sm text-gray-900">
                                        {{ event.formatted_date }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ event.formatted_start_time }}
                                    </div>
                                </td>

                                <td
                                    class="hidden px-6 py-4 text-sm whitespace-nowrap text-gray-500 lg:table-cell"
                                >
                                    {{ event.platform_name }}
                                </td>

                                <td
                                    class="hidden px-6 py-4 text-sm whitespace-nowrap text-gray-500 lg:table-cell"
                                >
                                    {{
                                        event.status
                                            .charAt(0)
                                            .toLocaleUpperCase() +
                                        event.status.slice(1)
                                    }}
                                </td>

                                <td
                                    class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                                >
                                    <div class="flex justify-end">
                                        <button
                                            @click="
                                                openRegistrationsModal(event)
                                            "
                                            class="text-gray-400 hover:cursor-pointer hover:text-gray-600"
                                            title="View Event Registrations"
                                        >
                                            <Eye class="size-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <Pagination :links="events.links" />
                </div>
            </div>
        </div>

        <Teleport to="body">
            <AttendeesListModal
                v-if="selectedEvent && isModalOpen"
                :is-open="isModalOpen"
                :selected-event="selectedEvent"
                @close-modal="closeModal"
            />
        </Teleport>
    </DashboardLayout>
</template>
