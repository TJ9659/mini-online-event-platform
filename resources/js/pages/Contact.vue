<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <section class="mb-16 text-center">
                    <h1 class="mb-4 text-3xl font-bold">Get in Touch</h1>
                    <p class="mb-8 text-gray-600">
                        Have a question or feedback? We'd love to hear from you.
                    </p>

                    <div class="grid gap-6 md:grid-cols-1">
                        <div class="rounded-lg border border-gray-300 p-6">
                            <h3 class="text-lg font-bold">Email Support</h3>
                            <p class="mt-2 text-blue-600 dark:text-blue-400">
                                <a href="mailto:support@example.com"
                                    >support@evently.com</a
                                >
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-300 p-6">
                            <h3 class="text-lg font-bold">Social Media</h3>
                            <p class="mt-2 text-gray-500">@evently-app</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
