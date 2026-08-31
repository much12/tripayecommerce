<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ArrowUpRight, DollarSign, Package, ShoppingCart, Users } from 'lucide-vue-next';

interface Stats {
    revenue: number;
    orders: number;
    products: number;
    customers: number;
}

interface Order {
    id: string;
    customer: string;
    total: number;
    status: string;
    date: string;
}

const props = defineProps<{
    stats: Stats;
    recentOrders: Order[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/admin' }];

function rupiah(value: number) {
    return 'Rp' + value.toLocaleString('id-ID');
}

const cards = [
    { label: 'Pendapatan', value: rupiah(props.stats.revenue), icon: DollarSign, trend: '+12,5%', color: 'text-emerald-500' },
    { label: 'Pesanan', value: props.stats.orders.toLocaleString('id-ID'), icon: ShoppingCart, trend: '+8,2%', color: 'text-emerald-500' },
    { label: 'Produk', value: props.stats.products.toLocaleString('id-ID'), icon: Package, trend: '+3 baru', color: 'text-blue-500' },
    { label: 'Pelanggan', value: props.stats.customers.toLocaleString('id-ID'), icon: Users, trend: '+5,1%', color: 'text-emerald-500' },
];

const statusStyle: Record<string, string> = {
    Selesai: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
    Dikirim: 'bg-blue-500/15 text-blue-600 dark:text-blue-400',
    Diproses: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
    Dibatalkan: 'bg-red-500/15 text-red-600 dark:text-red-400',
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Selamat datang, Admin 👋</h1>
                <p class="text-muted-foreground">Ringkasan performa toko Tripay hari ini.</p>
            </div>

            <!-- Stat cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="c in cards"
                    :key="c.label"
                    class="rounded-xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground">{{ c.label }}</span>
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <component :is="c.icon" class="h-4 w-4" />
                        </span>
                    </div>
                    <div class="mt-3 text-2xl font-bold">{{ c.value }}</div>
                    <div class="mt-1 flex items-center gap-1 text-xs" :class="c.color">
                        <ArrowUpRight class="h-3.5 w-3.5" /> {{ c.trend }}
                        <span class="text-muted-foreground">vs bulan lalu</span>
                    </div>
                </div>
            </div>

            <!-- Recent orders -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                <div class="flex items-center justify-between border-b border-sidebar-border/70 p-5 dark:border-sidebar-border">
                    <h2 class="font-semibold">Pesanan Terbaru</h2>
                    <a href="/admin/pesanan" class="text-sm font-medium text-primary hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                                <th class="px-5 py-3 font-medium">Invoice</th>
                                <th class="px-5 py-3 font-medium">Pelanggan</th>
                                <th class="px-5 py-3 font-medium">Tanggal</th>
                                <th class="px-5 py-3 font-medium">Total</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="o in recentOrders"
                                :key="o.id"
                                class="border-b border-sidebar-border/40 last:border-0 hover:bg-muted/40 dark:border-sidebar-border/40"
                            >
                                <td class="px-5 py-3 font-medium">{{ o.id }}</td>
                                <td class="px-5 py-3">{{ o.customer }}</td>
                                <td class="px-5 py-3 text-muted-foreground">{{ o.date }}</td>
                                <td class="px-5 py-3 font-medium">{{ rupiah(o.total) }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusStyle[o.status]">
                                        {{ o.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
