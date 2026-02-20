<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const props = defineProps<{
    selectedEvent: any;
    isOpen: boolean;
}>();

const emit = defineEmits(['close-modal']);

const updateOpen = (val: boolean) => {
    if (!val) emit('close-modal');
};
</script>

<template>
    <Dialog :open="isOpen" @update:open="updateOpen">
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle class="text-xl font-bold">
                    {{ selectedEvent?.title }} - Attendees
                </DialogTitle>
            </DialogHeader>

            <div class="max-h-[60vh] overflow-y-auto pr-2">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase">
                            <th class="pb-2">Name</th>
                            <th class="pb-2">Email</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="ticket in selectedEvent?.tickets"
                            :key="ticket.id"
                            class="hover:bg-gray-50 transition-colors"
                        >
                            <td class="py-3 text-sm font-medium">{{ ticket.user.name }}</td>
                            <td class="py-3 text-sm text-gray-600">
                                {{ ticket.user.email }}
                            </td>
                             <td class="py-3 text-sm font-medium"><span class="capitalize px-2 py-1 rounded-lg" :class="ticket.status === 'cancelled' ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500'">{{ ticket.status }}</span></td>
                        </tr>
                        <tr v-if="!selectedEvent?.tickets?.length">
                            <td colspan="2" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <span>No registrations yet.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </DialogContent>
    </Dialog>
</template>