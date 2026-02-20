<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import Spinner from '../ui/spinner/Spinner.vue';

defineProps<{
    open: boolean;
    title: string;
    description: string;
    loading: boolean;
}>();

const emit = defineEmits(['update:open', 'confirm']);
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>{{ description }}</DialogDescription>
            </DialogHeader>

            <DialogFooter>
                <Button variant="ghost" @click="emit('update:open', false)">
                    Cancel
                </Button>
                <Button class="text-white bg-orange-400 hover:bg-orange-500" @click="emit('confirm')">
                    <Spinner v-if="loading" />
                    Confirm
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
