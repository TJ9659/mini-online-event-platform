<script setup lang="ts">
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    event: any;
    availableCategories: Array<{
        id: number;
        name: string;
    }>;
}>();

const previewUrl = ref(
    props.event.cover_image !== 'default-event-banner.png'
        ? `/storage/${props.event.cover_image}`
        : null,
);

const parseDateTime = (dateTimeString: string | null) => {
    if (!dateTimeString) return { hours: 9, minutes: 0 };

    const timePart = dateTimeString.split(' ')[1];

    const [hours, minutes] = timePart.split(':');

    return {
        hours: parseInt(hours),
        minutes: parseInt(minutes),
    };
};

const form = useForm({
    _method: 'put',
    title: props.event.title,
    description: props.event.description || '',
    platform_name: props.event.platform_name || '',
    meeting_link: props.event.meeting_link || '',
    date: props.event.start_time ? props.event.start_time.split(' ')[0] : '',
    start_time: parseDateTime(props.event.start_time),
    end_time: parseDateTime(props.event.end_time),
    cover_image: props.event.cover_image as File | null,
    capacity: props.event.capacity || 0,
    speaker: props.event.speaker || '',
    timezone: props.event.timezone,
    category_ids: props.event.categories.map((c: any) => c.id),
});

watch(
    () => form.start_time,
    (newStart) => {
        if (newStart && form.end_time) {
            const startVal = newStart.hours * 60 + newStart.minutes;
            const endVal = form.end_time.hours * 60 + form.end_time.minutes;

            if (startVal >= endVal) {
                form.end_time = {
                    hours: (newStart.hours + 1) % 24, // once the user sets the start time, the end time will be automatically pushed to an hour more than when started. (i.e. st: 1pm, et: 2pm)
                    minutes: newStart.minutes,
                };
            }
        }
    },
    { deep: true },
);

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.cover_image = file;

        previewUrl.value = URL.createObjectURL(file);
    }
};

const removeFile = () => {
    form.cover_image = null;
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
    }
};

const isSelected = (id: number) => form.category_ids.includes(id);
const toggleCategory = (id: number) => {
    if (isSelected(id)) {
        form.category_ids = form.category_ids.filter(
            (itemId: number) => itemId !== id,
        );
    } else {
        form.category_ids.push(id);
    }
};

const submit = (status: 'draft' | 'published') => {
    if (status === 'published' && !isFormValid.value) return;

    form.transform((data) => ({
        ...data,
        status: status,
        cover_image: data.cover_image instanceof File ? data.cover_image : null,
        start_time: data.date
            ? `${data.date} ${String(data.start_time.hours).padStart(2, '0')}:${String(data.start_time.minutes).padStart(2, '0')}:00`
            : null,
        end_time: data.date
            ? `${data.date} ${String(data.end_time.hours).padStart(2, '0')}:${String(data.end_time.minutes).padStart(2, '0')}:00`
            : null, // padding for time
        clear_image: previewUrl.value === null,
    })).post(`/manage/events/${props.event.id}`, {
        forceFormData: true,
        preserveScroll: true,
        replace: true,
        onError: (err) => {
            console.error('Server/Network Error:', err);
        },
        onFinish: () => {
            if (form.hasErrors) {
                console.log('Validation Errors:', form.errors);
            }
        },
        onSuccess: () => {},
    });
};

const isFormValid = computed(() => {
    const hasImage =
        form.cover_image !== null ||
        (props.event.cover_image &&
            props.event.cover_image !== 'default-event-banner.png' &&
            previewUrl.value !== null);
    return (
        canSaveDraft.value &&
        form.title.trim() !== '' &&
        form.description.trim() !== '' &&
        form.date !== '' &&
        form.platform_name.trim() !== '' &&
        form.meeting_link.trim() !== '' &&
        form.speaker.trim() !== '' &&
        form.capacity > 0 &&
        form.category_ids.length > 0 &&
        hasImage
    );
});

const canSaveDraft = computed(() => form.title.trim() !== '');

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Edit Event',
        href: `/manage/events/${props.event.id}/edit`,
    },
];
</script>

<template>
    <Head title="Edit Event" />
    <DashboardLayout :breadcrumbs="breadcrumbItems">
        <div class="mx-auto max-w-6xl p-4 sm:p-8">
            <h1 class="mb-6 text-2xl font-bold">
                Edit Event {{ props.event.title }}
            </h1>

            <form @submit.prevent class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-2 md:col-span-2">
                    <label class="block font-medium">Event Title</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full rounded-lg border p-2"
                        placeholder="e.g. Tech Conference 2026"
                    />
                    <p v-if="form.errors.title" class="text-sm text-red-500">
                        {{ form.errors.title }}
                    </p>
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="block font-medium">Description</label>
                    <textarea
                        v-model="form.description"
                        class="w-full rounded-lg border p-2"
                        rows="8"
                    ></textarea>
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="block font-medium">Event Categories</label>
                    <div class="space-y-2 space-x-2">
                        <button
                            v-for="cat in availableCategories"
                            :key="cat.id"
                            type="button"
                            @click="toggleCategory(cat.id)"
                            :class="[
                                'rounded-full border px-4 py-2 text-sm font-medium transition-colors',
                                isSelected(cat.id)
                                    ? 'border-orange-600 bg-orange-500 text-white'
                                    : 'border-gray-300 bg-white text-gray-700 hover:border-orange-500',
                            ]"
                        >
                            {{ cat.name }}
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block font-medium">Speaker</label>
                    <input
                        v-model="form.speaker"
                        type="text"
                        class="w-full rounded-lg border p-2"
                    />
                </div>

                <div class="space-y-2">
                    <label class="block font-medium">Capacity</label>
                    <input
                        v-model="form.capacity"
                        type="number"
                        class="w-full rounded-lg border p-2"
                    />
                </div>

                <div class="space-y-2">
                    <label class="block font-medium">Platform Name</label>
                    <input
                        v-model="form.platform_name"
                        type="text"
                        class="w-full rounded-lg border p-2"
                        placeholder="Zoom, Google Meet, etc."
                    />
                </div>

                <div class="space-y-2">
                    <label class="block font-medium">Meeting Link</label>
                    <input
                        v-model="form.meeting_link"
                        type="url"
                        class="w-full rounded-lg border p-2"
                    />
                </div>
                <div class="space-y-2">
                    <label class="block font-medium">Event Date</label>
                    <input
                        v-model="form.date"
                        type="date"
                        class="w-full rounded-lg border p-2"
                    />
                </div>
                <div>
                    <div class="space-y-2">
                        <label class="block font-medium">Start Time</label>
                        <VueDatePicker
                            v-model="form.start_time"
                            time-picker
                            teleport="body"
                            input-class-name="w-full rounded-lg border p-2"
                        />
                        <p
                            v-if="form.errors.start_time"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.start_time }}
                        </p>
                        <!-- <input
                            v-model="form.start_time"
                            type="time"
                            class="w-full rounded-lg border p-2"
                        /> -->
                    </div>

                    <div class="space-y-2">
                        <label class="block font-medium">End Time</label>
                        <VueDatePicker
                            v-model="form.end_time"
                            time-picker
                            :min-time="form.start_time"
                            teleport="body"
                            input-class-name="w-full rounded-lg border p-2"
                        />
                        <p
                            v-if="form.errors.end_time"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.end_time }}
                        </p>
                    </div>
                    <!-- <input
                            v-model="form.end_time"
                            :min="form.start_time"
                            type="time"
                            class="w-full rounded-lg border p-2"
                        /> -->
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="block font-medium">Cover Image</label>

                    <input
                        v-if="!form.cover_image"
                        type="file"
                        @input="handleFileSelect"
                        class="w-full text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-orange-400 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-orange-500"
                        accept="image/*"
                    />
                    <div
                        v-if="previewUrl"
                        class="relative mt-4 h-auto max-w-[500px]"
                    >
                        <img
                            :src="previewUrl"
                            class="h-auto w-full rounded-lg object-cover shadow-md"
                        />

                        <button
                            type="button"
                            @click="removeFile"
                            class="absolute top-2 right-2 rounded-full bg-orange-600 p-2 text-white shadow-lg hover:bg-orange-700"
                            title="Remove Image"
                        >
                            <X color="white" />
                        </button>
                    </div>

                    <p
                        v-if="form.errors.cover_image"
                        class="text-sm text-red-500"
                    >
                        {{ form.errors.cover_image }}
                    </p>
                </div>

                <div
                    class="flex items-center justify-between gap-4 border-t pt-4 md:col-span-2"
                >
                    <button
                        type="button"
                        :disabled="form.processing || !canSaveDraft"
                        @click="submit('draft')"
                        class="rounded-lg bg-gray-600 px-6 py-2 font-bold text-white hover:bg-gray-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Saving' : 'Save as draft' }}
                    </button>

                    <button
                        type="button"
                        :disabled="form.processing || !isFormValid"
                        @click="submit('published')"
                        class="rounded-lg bg-orange-600 px-6 py-2 font-bold text-white hover:bg-orange-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Publishing...' : 'Edit Event' }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
