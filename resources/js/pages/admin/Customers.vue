<script setup lang="ts">
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Search, Users } from 'lucide-vue-next';
import { ref } from 'vue';

interface Customer {
    id: number;
    name: string;
    email: string;
    created_at: string;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    customers: Paginated<Customer>;
    filters: { search: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Pelanggan', href: '/admin/pelanggan' },
];

const search = ref(props.filters.search ?? '');

function reload() {
    router.get('/admin/pelanggan', { search: search.value }, { preserveState: true, replace: true });
}

watchDebounced(search, reload, { debounce: 350 });

function formatDate(iso: string) {
    return new Date(iso).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
    <Head title="Kelola Pelanggan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Kelola Pelanggan</h1>
                <p class="text-muted-foreground">Daftar pelanggan yang terdaftar di toko.</p>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative max-w-sm flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Cari nama atau email pelanggan..." class="pl-9" />
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                                <th class="px-5 py-3 font-medium">ID</th>
                                <th class="px-5 py-3 font-medium">Nama</th>
                                <th class="px-5 py-3 font-medium">Email</th>
                                <th class="px-5 py-3 font-medium">Terdaftar Pada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="customer in customers.data"
                                :key="customer.id"
                                class="border-b border-sidebar-border/40 last:border-0 hover:bg-muted/40 dark:border-sidebar-border/40"
                            >
                                <td class="px-5 py-3 font-medium">{{ customer.id }}</td>
                                <td class="px-5 py-3">{{ customer.name }}</td>
                                <td class="px-5 py-3 text-muted-foreground">{{ customer.email }}</td>
                                <td class="px-5 py-3 text-xs text-muted-foreground">{{ formatDate(customer.created_at) }}</td>
                            </tr>

                            <tr v-if="customers.data.length === 0">
                                <td colspan="4">
                                    <div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                            <Users class="h-6 w-6" />
                                        </span>
                                        <p class="font-medium">Tidak ada pelanggan</p>
                                        <p class="max-w-xs text-sm text-muted-foreground">Belum ada pelanggan yang terdaftar atau sesuai dengan pencarian.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="customers.total > 0" class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-muted-foreground">Menampilkan {{ customers.from }}–{{ customers.to }} dari {{ customers.total }} pelanggan</p>
                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, i) in customers.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-state
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-sidebar-border/70 px-3 text-sm transition-colors hover:bg-muted dark:border-sidebar-border"
                            :class="link.active ? 'bg-primary text-primary-foreground hover:bg-primary' : ''"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded-md px-3 text-sm text-muted-foreground opacity-50"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>