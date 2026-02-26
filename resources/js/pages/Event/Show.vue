<script setup lang="ts">
import CustomDialog from '@/components/custom/CustomDialog.vue';
import EventCard from '@/components/event/EventCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Calendar, Camera, Link as LinkIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const isDialogOpen = ref(false);
const isLoading = ref(false);

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps<{
    event: {
        id: number;
        title: string;
        slug: string;
        description: string;
        platform_name: string;
        cover_image: string;
        formatted_date: string;
        formatted_start_time: string;
        formatted_end_time: string;
        meeting_link: string;
        categories: {
            id: number;
            name: string;
            slug: string;
        }[];
        capacity: number;
        organizer: {
            id: number;
            name: string;
        };
    };
    similarEvents: Array<{
        id: number;
        title: string;
        slug: string;
        description: string;
        platform_name: string;
        cover_image: string;
        formatted_date: string;
        formatted_start_time: string;
        formatted_end_time: string;
        meeting_link: string;
        categories: {
            id: number;
            name: string;
            slug: string;
        }[];
        capacity: number;
        organizer: {
            id: string;
            name: string;
        };
    }>;
    hasTicket: boolean;
    ticketStatus: string;
    isOrganizer: boolean;
    isEventFull: boolean;
}>();

const handleAttendDialogOpen = () => {
    if (!page.props.auth.user) {
        window.location.href = '/login';
        return;
    }

    isDialogOpen.value = true;
};

const handleAttend = () => {
    isLoading.value = true;

    router.post(
        `/events/${props.event.id}/tickets`,
        {},
        {
            onSuccess: () => {
                isDialogOpen.value = false;
                console.log('Ticket created successfully!');
            },
            onError: (errors) => {
                console.error('Error creating ticket:', errors);
            },
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
};
</script>

<template>
    <Head :title="event.title" />

    <AppLayout>
        <div class="mx-auto max-w-6xl">
            <div class="mb-8 flex justify-center overflow-hidden rounded-xl">
                <img
                    :src="'/storage/' + event.cover_image"
                    class="h-[200px] w-auto rounded-xl object-cover md:h-[500px]"
                    sizes="(max-width: 1920px) 100vw, 1920px"
                    fetchpriority="high"
                />
            </div>

            <div class="mb-4 grid grid-cols-1 gap-12 lg:grid-cols-3">
                <div class="space-y-8 lg:col-span-2">
                    <section>
                        <h1
                            class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl"
                        >
                            {{ event.title }}
                        </h1>
                        <div class="mt-4 text-lg leading-relaxed text-gray-600">
                            By
                            <span class="font-bold">{{
                                event.organizer.name
                            }}</span>
                        </div>

                        <div
                            class="mt-4 flex items-center gap-2 text-lg leading-relaxed text-gray-600"
                        >
                            <Camera />
                            {{ event.platform_name }}
                        </div>

                        <div
                            class="mt-4 flex items-center gap-2 text-lg leading-relaxed text-gray-600"
                        >
                            <Calendar />
                            <div>
                                <span
                                    >{{ event.formatted_date }} from
                                    {{ event.formatted_start_time }} to
                                    {{ event.formatted_end_time }}</span
                                >
                            </div>
                        </div>

                        <div
                            v-if="event.meeting_link"
                            class="mt-4 flex items-center gap-2 text-lg leading-relaxed text-gray-600"
                        >
                            <LinkIcon />
                            {{ event.meeting_link }}
                        </div>
                    </section>

                    <section class="border-t pt-8">
                        <h2 class="text-2xl font-bold text-gray-900">
                            Overview
                        </h2>
                        <p
                            class="mt-2 text-gray-600"
                            style="white-space: pre-line"
                        >
                            {{ event.description }}
                        </p>
                        <div class="text-md mt-4 leading-relaxed text-gray-600">
                            <span>Category:</span>
                            <span
                                :href="'/categories/' + category.slug"
                                v-for="(category, index) in event.categories"
                                :key="category.id"
                            >
                                <Link
                                    :href="'/categories/' + category.slug"
                                    class="hover:underline"
                                    >{{ category.name }}</Link
                                >
                                <span v-if="index < event.categories.length - 1"
                                    >,
                                </span>
                            </span>
                        </div>
                    </section>

                    <section class="border-t pt-8">
                        <h2 class="mb-4 text-2xl font-bold text-gray-900">
                            Organized by
                        </h2>
                        <div
                            class="flex flex-col gap-5 bg-gray-100 p-5 md:flex-row"
                        >
                            <p class="font-bold">
                                {{ event.organizer.name }}
                            </p>
                        </div>
                    </section>
                </div>

                <div class="lg:col-span-1">
                    <div
                        class="sticky top-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
                    >
                        <div class="space-y-4">
                            <div class="text-center">
                                <span class="text-2xl font-bold text-gray-900"
                                    >Free</span
                                >
                            </div>

                            <button
                                v-if="!user"
                                class="w-full rounded-lg bg-orange-500 px-4 py-3 font-bold text-white transition hover:cursor-pointer hover:bg-orange-600"
                                @click="handleAttendDialogOpen"
                            >
                                Log in to register
                            </button>

                            <button
                                v-else-if="isOrganizer"
                                class="w-full cursor-default rounded-lg bg-gray-600 px-4 py-3 font-bold text-white"
                                disabled
                            >
                                You are the Organizer
                            </button>

                            <button
                                v-else-if="hasTicket"
                                class="w-full cursor-default rounded-lg bg-gray-700 px-4 py-3 font-bold text-white"
                                disabled
                            >
                                Already Registered
                            </button>

                            <button
                                v-else-if="ticketStatus === 'cancelled'"
                                class="w-full cursor-default rounded-lg bg-red-500 px-4 py-3 font-bold text-white"
                                disabled
                            >
                                Cancelled
                            </button>

                            <button
                                v-else-if="props.isEventFull"
                                class="w-full cursor-default rounded-lg bg-gray-600 px-4 py-3 font-bold text-white"
                                disabled
                            >
                                Event full
                            </button>

                            <button
                                v-else
                                class="w-full rounded-lg bg-orange-400 px-4 py-3 font-bold text-white transition hover:bg-orange-500 disabled:opacity-50"
                                @click="handleAttendDialogOpen"
                                :disabled="isLoading"
                            >
                                {{
                                    isLoading
                                        ? 'Processing...'
                                        : 'Confirm Attendance'
                                }}
                            </button>

                            <div class="border-t pt-4 text-sm text-gray-500">
                                <p>
                                    <strong>Date:</strong>
                                    {{ event.formatted_date }}
                                </p>
                                <p>
                                    <strong>Time:</strong>
                                    {{ event.formatted_start_time }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <section class="border-t pt-4">
                <h2 class="mb-4 text-2xl font-bold text-gray-900">
                    Other events you may like
                </h2>
                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4"
                >
                    <Link
                        :href="'/events/' + event.slug"
                        v-for="event in similarEvents"
                        :key="event.id"
                    >
                        <EventCard :event="event" />
                    </Link>
                </div>
            </section>
        </div>

        <Teleport to="body">
            <CustomDialog
                v-model:open="isDialogOpen"
                :title="'Confirm your attendance to ' + event.title"
                description="By attending this event, your spot will be reserved. You can view or manage your attendance later from your profile."
                @confirm="handleAttend"
                :loading="isLoading"
            />
        </Teleport>
    </AppLayout>
</template>
