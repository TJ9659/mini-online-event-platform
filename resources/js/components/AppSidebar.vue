<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
// import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    CalendarClock,
    CalendarPlus,
    CirclePlus,
    Folder,
    Pencil,
    Ticket,
    UserPen,
    Users,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    // {
    //     title: 'Dashboard',
    //     href: dashboard(),
    //     icon: LayoutGrid,
    // },
    {
        title: 'Profile',
        href: '/settings/profile',
        icon: UserPen,
    },
    {
        title: 'Events',
        href: '/manage/events',
        icon: CalendarPlus,
        items: [
            {
                title: 'All Events',
                href: '/manage/events',
                icon: CalendarClock,
            },
            {
                title: 'Create Event',
                href: '/manage/events/create',
                icon: CirclePlus,
            },
            {
                title: 'Drafts',
                href: '/manage/events/drafts',
                icon: Pencil,
            },
            {
                title: 'Registrations',
                href: '/manage/events/registrations',
                icon: Users,
            },
        ],
    },
    {
        title: 'My Tickets',
        href: '/my-tickets',
        icon: Ticket,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="'/'">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
