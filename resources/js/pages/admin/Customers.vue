<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Users } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Pelanggan', href: '/admin/pelanggan' },
];
</script>

<template>
    <Head title="Kelola Pelanggan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Kelola Pelanggan</h1>
                <p class="text-muted-foreground">Daftar pelanggan yang terdaftar di toko.</p>
            </div>

            <div class="flex flex-1 flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-sidebar-border/70 py-24 text-center dark:border-sidebar-border">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                    <Users class="h-7 w-7" />
                </span>
                <h2 class="text-lg font-semibold">Manajemen pelanggan segera hadir</h2>
                <p class="max-w-sm text-sm text-muted-foreground">
                    Lihat data pelanggan, riwayat pembelian, dan kelola akun dari halaman ini.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
