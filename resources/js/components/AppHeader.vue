<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuList,
    NavigationMenuTrigger,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { useActiveUrl } from '@/composables/useActiveUrl';
import { getInitials } from '@/composables/useInitials';
import { toUrl } from '@/lib/utils';
import type { BreadcrumbItem, NavItem } from '@/types';
import type { InertiaLinkProps } from '@inertiajs/vue3';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    BookOpen,
    ChevronLeft,
    Folder,
    Menu,
    Plus,
    Search,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

interface Category {
    id: number;
    name: string;
    slug: string;
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);
const { urlIsActive } = useActiveUrl();
const searchQuery = ref('');

const categories = computed<Category[]>(
    () => page.props.categories as Category[],
);

function activeItemStyles(url: NonNullable<InertiaLinkProps['href']>) {
    return urlIsActive(url)
        ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100'
        : '';
}

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Events',
        href: '/events',
    },
    {
        title: 'Categories',
        icon: ArrowRight,
        items: categories.value.map((category) => ({
            title: category.name,
            href: `/categories/${category.slug}`,
        })),
    },
    {
        title: 'Create an event',
        href: '/manage/events/create',
        icon: Plus,
    },
]);

const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

const handleSearch = () => {
    router.get(
        '/events',
        { search: searchQuery.value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const currentMobileView = ref<boolean>(true);
const switchToMain = () => (currentMobileView.value = true);

const handleSheetOpenChange = (open: boolean) => {
    if (!open) {
        setTimeout(() => (currentMobileView.value = true), 300);
    }
};

const activeSubItems = ref<NavItem[] | null>(null);

const switchToSubMenu = (items: NavItem[]) => {
    activeSubItems.value = items;
    currentMobileView.value = false;
};
</script>

<template>
    <div>
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="mr-2 h-9 w-9"
                            >
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent
                            side="left"
                            class="w-[280px] p-6"
                            :interact-outside="handleSheetOpenChange"
                        >
                            <SheetTitle class="sr-only"
                                >Navigation Menu</SheetTitle
                            >

                            <div class="flex items-center gap-2 border-b pb-4">
                                <Button
                                    v-if="!currentMobileView"
                                    variant="ghost"
                                    size="icon"
                                    @click="switchToMain"
                                    class="-ml-2"
                                >
                                    <ChevronLeft class="h-5 w-5" />
                                </Button>
                                <span class="text-lg font-bold">
                                    {{
                                        currentMobileView
                                            ? 'Menu'
                                            : 'Categories'
                                    }}
                                </span>
                            </div>

                            <div class="flex h-full flex-1 flex-col py-2">
                                <nav
                                    v-if="currentMobileView"
                                    class="space-y-1"
                                >
                                    <template
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                    >
                                        <button
                                            v-if="item.items"
                                            @click="switchToSubMenu(item.items)"
                                            class="flex w-full items-center justify-between px-3 py-2 text-base font-medium hover:bg-accent"
                                        >
                                            {{ item.title }}
                                            <component
                                                v-if="item.icon"
                                                :is="item.icon"
                                                class="h-5 w-5"
                                            />
                                        </button>

                                        <Link
                                            v-else
                                            :href="item.href"
                                            class="flex items-center gap-x-2 px-3 py-2 text-base font-medium hover:bg-accent"
                                        >
                                            {{ item.title }}
                                            <component
                                                v-if="item.icon"
                                                :is="item.icon"
                                                class="h-4 w-4"
                                            />
                                        </Link>
                                    </template>
                                </nav>

                                <nav v-else class="space-y-1">
                                    <Link
                                        v-for="sub in activeSubItems"
                                        :key="sub.href"
                                        :href="sub.href"
                                        class="flex items-center px-3 py-2 text-base hover:bg-accent"
                                    >
                                        {{ sub.title }}
                                    </Link>
                                </nav>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
                <div class="flex items-center gap-x-4">
                    <Link :href="'/'">
                        <AppLogo />
                    </Link>

                    <div
                        class="flex hidden flex-row items-center justify-around rounded-lg border border-gray-500 px-3 py-1 transition-all duration-200 focus-within:border-orange-500 focus-within:ring-1 focus-within:ring-orange-500 sm:flex"
                    >
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search..."
                            aria-label="Search"
                            class="bg-transparent outline-none"
                            @keydown.enter="handleSearch"
                        />
                        <Button
                            variant="ghost"
                            size="icon"
                            class="group h-9 w-9 cursor-pointer"
                            @click="handleSearch"
                        >
                            <Search
                                class="size-5 opacity-80 transition-colors group-hover:text-orange-500 group-hover:opacity-100"
                            />
                        </Button>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 flex h-full items-stretch">
                        <NavigationMenuList
                            class="flex h-full items-stretch space-x-2"
                        >
                            <NavigationMenuItem
                                v-for="item in mainNavItems"
                                :key="item.title"
                                class="relative flex h-full items-center"
                            >
                                <template v-if="item.items">
                                    <NavigationMenuTrigger
                                        :class="navigationMenuTriggerStyle()"
                                    >
                                        {{ item.title }}
                                    </NavigationMenuTrigger>
                                    <NavigationMenuContent
                                        align="start"
                                        class="w-48 p-2"
                                    >
                                        <Link
                                            v-for="subItem in item.items"
                                            :key="subItem.href"
                                            :href="subItem.href"
                                            class="block rounded-md px-2 py-2 text-sm hover:bg-accent"
                                        >
                                            {{ subItem.title }}
                                        </Link>
                                    </NavigationMenuContent>
                                </template>

                                <template v-else>
                                    <Link
                                        :href="item.href"
                                        :class="[
                                            navigationMenuTriggerStyle(),
                                            activeItemStyles(item.href),
                                        ]"
                                    >
                                        <component
                                            v-if="item.icon"
                                            :is="item.icon"
                                            class="mr-2 h-4 w-4"
                                        />
                                        {{ item.title }}
                                    </Link>
                                    <div
                                        v-if="urlIsActive(item.href)"
                                        class="absolute bottom-0 left-0 h-0.5 w-full bg-black dark:bg-white"
                                    />
                                </template>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="ml-auto flex items-center space-x-2">
                    <div class="relative flex items-center space-x-1">
                        <div class="hidden space-x-1 lg:flex">
                            <!-- <template
                                v-for="item in rightNavItems"
                                :key="item.title"
                            >
                                <TooltipProvider :delay-duration="0">
                                    <Tooltip>
                                        <TooltipTrigger>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                as-child
                                                class="group h-9 w-9 cursor-pointer"
                                            >
                                                <a
                                                    :href="toUrl(item.href)"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <span class="sr-only">{{
                                                        item.title
                                                    }}</span>
                                                    <component
                                                        :is="item.icon"
                                                        class="size-5 opacity-80 group-hover:opacity-100"
                                                    />
                                                </a>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{ item.title }}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </template> -->
                        </div>
                    </div>

                    <template v-if="auth.user">
                        <DropdownMenu>
                            <DropdownMenuTrigger :as-child="true">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                                >
                                    <Avatar
                                        class="size-8 overflow-hidden rounded-full"
                                    >
                                        <AvatarImage
                                            v-if="auth.user.avatar"
                                            :src="auth.user.avatar"
                                            :alt="auth.user.name"
                                        />
                                        <AvatarFallback
                                            class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                                        >
                                            {{ getInitials(auth.user?.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56">
                                <UserMenuContent :user="auth.user" />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </template>

                    <template v-else>
                        <div class="flex items-center gap-4">
                            <Link href="/login" class="text-sm font-medium"
                                >Log in</Link
                            >
                            <Button
                                class="bg-orange-500 text-white hover:bg-orange-600"
                                as-child
                            >
                                <Link href="/register">Get Started</Link>
                            </Button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex flex-1 justify-center px-4">
                <div
                    class="block flex w-full max-w-[400px] flex-row items-center rounded-lg border border-gray-500 px-3 py-1 transition-all duration-200 focus-within:border-orange-500 focus-within:ring-1 focus-within:ring-orange-500 sm:hidden"
                >
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search..."
                        class="w-full bg-transparent text-sm outline-none"
                        @keydown.enter="handleSearch"
                    />
                    <Button
                        variant="ghost"
                        size="icon"
                        class="group h-8 w-8 cursor-pointer"
                        @click="handleSearch"
                    >
                        <Search
                            class="size-4 opacity-80 transition-colors group-hover:text-orange-500"
                        />
                    </Button>
                </div>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs.length > 1"
            class="flex w-full border-b border-sidebar-border/70"
        >
            <div
                class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl"
            >
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
