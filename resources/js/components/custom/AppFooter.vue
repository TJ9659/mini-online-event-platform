<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
const currentYear = new Date().getFullYear();
</script>

<template>
    <footer
        class="border-t border-gray-200 bg-white px-4 py-4 dark:border-gray-800 dark:bg-zinc-950"
    >
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 md:flex-row"
        >
            <p class="text-xs text-gray-500 dark:text-gray-400">
                &copy; {{ currentYear }} Evently. All rights reserved.
                <a
                    href="https://github.com/TJ9659"
                    target="_blank"
                    class="hover:underline"
                    >Developed by @TJ9659 on GitHub.</a
                >
            </p>

            <nav
                class="flex gap-4 text-xs font-medium text-gray-600 dark:text-gray-300"
            >
                <Link
                    href="/about-us"
                    class="transition-colors hover:text-black dark:hover:text-white"
                    >About Us</Link
                >
                <Link
                    href="/privacy"
                    class="transition-colors hover:text-black dark:hover:text-white"
                    >Privacy Policy</Link
                >
                <Link
                    href="/terms"
                    class="transition-colors hover:text-black dark:hover:text-white"
                    >Terms of Service</Link
                >
                <Link
                    href="/contact"
                    class="transition-colors hover:text-black dark:hover:text-white"
                    >Contact</Link
                >
            </nav>
        </div>
    </footer>
</template>
