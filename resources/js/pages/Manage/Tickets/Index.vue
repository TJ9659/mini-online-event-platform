<script setup lang="ts">
import CustomDialog from '@/components/custom/CustomDialog.vue';
import Pagination from '@/components/custom/Pagination.vue';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Ban, ExternalLink } from 'lucide-vue-next';
import { ref } from 'vue';

interface Ticket {
    id: number;
    status: string;
    user_id: number;
    event_id: number;
    event: {
        id: number;
        title: string;
        slug: string;
        platform_name: string;
        formatted_date: string;
        formatted_start_time: string;
        capacity: number;
        status: string;
        meeting_link: string;
    };
}
defineProps<{
    tickets: {
        data: Array<{
            id: number;
            status: string;
            user_id: number;
            event_id: number;
            event: {
                id: number;
                title: string;
                slug: string;
                platform_name: string;
                formatted_date: string;
                formatted_start_time: string;
                capacity: number;
                status: string;
                meeting_link: string;

                links: any;
            };
        }>;
        links: any;
    };
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'My Tickets',
        href: '/my-tickets',
    },
];

const selectedTicket = ref<Ticket | null>(null);
const isCancelDialogOpen = ref(false);
const isLoading = ref(false);

const handleCancelDialogOpen = (ticket: any) => {
    if (!ticket) return;
    selectedTicket.value = ticket;
    isCancelDialogOpen.value = true;
};

const handleCancel = (ticketId: number) => {
    console.log(selectedTicket);
    if (!ticketId) return;
    isLoading.value = true;

    router.put(
        `/manage/tickets/${ticketId}/cancel`,
        {},
        {
            onSuccess: () => {
                isCancelDialogOpen.value = false;
                console.log('Ticket cancelled successfully!');
            },
            onError: (errors) => {
                console.error('Error cancelling ticket:', errors);
            },
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
};
</script>

<template>
    <Head title="My Tickets" />

    <DashboardLayout :breadcrumbs="breadcrumbItems">
        <div class="min-w-0 flex-1">
            <div class="mx-auto max-w-6xl p-4 sm:p-8">
                <div
                    class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <h1 class="text-2xl font-bold text-gray-900">My Tickets</h1>
                </div>

                <div v-if="tickets.data.length === 0">
                    <p class="text-lg text-gray-500">
                        You currently have no tickets.
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
                                    class="px-6 py-3 text-right text-xs font-semibold tracking-wider text-gray-500 uppercase"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="ticket in tickets.data"
                                :key="ticket.id"
                                class="transition-colors hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ ticket.event.title }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-500 sm:hidden"
                                    >
                                        {{ ticket.event.formatted_date }}
                                    </div>
                                </td>

                                <td
                                    class="hidden px-6 py-4 whitespace-nowrap sm:table-cell"
                                >
                                    <div class="text-sm text-gray-900">
                                        {{ ticket.event.formatted_date }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ ticket.event.formatted_start_time }}
                                    </div>
                                </td>

                                <td
                                    class="hidden px-6 py-4 text-sm whitespace-nowrap text-gray-500 lg:table-cell"
                                >
                                    {{ ticket.event.platform_name }}
                                </td>

                                <td
                                    class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                                >
                                    <div
                                        v-if="ticket.status !== 'cancelled'"
                                        class="flex justify-end gap-2"
                                    >
                                        <a
                                            :href="ticket.event.meeting_link"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-gray-400 hover:cursor-pointer hover:text-gray-600"
                                            title="Open Event Link"
                                        >
                                            <ExternalLink class="size-5" />
                                        </a>
                                        <button
                                            @click="
                                                handleCancelDialogOpen(ticket)
                                            "
                                            class="text-red-400 hover:cursor-pointer hover:text-red-600"
                                            title="Cancel Event Registration"
                                        >
                                            <Ban class="size-5" />
                                        </button>
                                    </div>
                                    <div
                                        v-else
                                        class="inline-block rounded-lg bg-red-50 px-3 py-1 text-xs font-medium text-red-500 select-none"
                                    >
                                        Cancelled
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <Pagination :links="tickets.links" />
                </div>
            </div>
        </div>

        <Teleport to="body">
            <CustomDialog
                v-model:open="isCancelDialogOpen"
                :title="
                    'Cancel your registration to the event ' +
                    selectedTicket?.event?.title +
                    '?'
                "
                description="By cancelling your registration, you will not be able to register again using this account."
                @confirm="handleCancel(selectedTicket?.id)"
                :loading="isLoading"
            />
        </Teleport>
    </DashboardLayout>
</template>
