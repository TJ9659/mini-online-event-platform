<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-3xl text-center">
                <h1>Privacy Policy</h1>
                <p>Last Updated: January 2026</p>

                <section class="mt-8">
                    <h2 class="text-xl font-semibold">
                        1. Information We Collect
                    </h2>
                    <p>
                        We collect information you provide directly to us, such
                        as when you create an account, update your profile, or
                        communicate with us.
                    </p>
                    <ul>
                        <li>Account information (Name, Email, Password)</li>
                        <li>
                            Usage data (IP address, browser type, pages visited)
                        </li>
                    </ul>

                    <h2 class="mt-6 text-xl font-semibold">
                        2. How We Use Information
                    </h2>
                    <p>
                        We use the information we collect to maintain and
                        improve our services, communicate with you, and protect
                        our users.
                    </p>

                    <h2 class="mt-6 text-xl font-semibold">3. Data Security</h2>
                    <p>
                        We take reasonable measures to protect your personal
                        information from loss, theft, and unauthorized access.
                    </p>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
