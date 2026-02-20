<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl">
                <section class="text-center mb-16">
                    <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl">
                        About Evently
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                        Evently was built as a modern solution for event management, focusing on 
                        seamless user experiences and clean data handling. This project explores 
                        the integration of Vue 3, TypeScript, and Laravel to create a high-performance web application.
                    </p>
                </section>

                <div class="grid gap-12">
                    <div class="rounded-2xl bg-gray-50 p-8 dark:bg-zinc-900 border dark:border-gray-800">
                        <h2 class="text-2xl font-bold mb-4">The Developer</h2>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Hi! I'm the developer behind this project. I'm passionate about building 
                            tools that are both functional and beautiful. This project serves as a 
                            portfolio piece to demonstrate my skills in full-stack development, 
                            UI/UX design, and software architecture.
                        </p>
                    </div>

                    <div class="rounded-2xl border-2 border-dashed border-amber-200 bg-amber-50 p-8 dark:border-amber-900/50 dark:bg-amber-950/20">
                        <h2 class="text-xl font-bold text-amber-800 dark:text-amber-400 mb-2">Important Notice</h2>
                        <p class="text-amber-700 dark:text-amber-500/80">
                            <strong>This is not a real application.</strong> Evently is a demonstration 
                            project created for educational and portfolio purposes. While the features 
                            may work, please do not enter sensitive personal data or expect real-world 
                            event fulfillment.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>