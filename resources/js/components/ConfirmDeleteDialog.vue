<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

withDefaults(
    defineProps<{
        open: boolean;
        title?: string;
        description?: string;
    }>(),
    {
        title: 'Eliminar element',
        description: "Segur que vols eliminar aquest element? Aquesta acció no es pot desfer.",
    },
);

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();
</script>

<template>
    <Dialog :open="open" @update:open="emit('cancel')">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>{{ description }}</DialogDescription>
            </DialogHeader>

            <DialogFooter>
                <button
                    type="button"
                    class="inline-flex cursor-pointer items-center rounded-xl border border-sidebar-border/80 bg-background px-4 py-2 text-sm font-medium transition hover:bg-muted"
                    @click="emit('cancel')"
                >
                    Cancel·lar
                </button>

                <button
                    type="button"
                    class="inline-flex cursor-pointer items-center rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                    @click="emit('confirm')"
                >
                    Eliminar
                </button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
