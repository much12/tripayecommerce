<script setup lang="ts">
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { CheckCircle2, Clock, Receipt, Search, Wallet } from 'lucide-vue-next';
import { ref } from 'vue';

interface Invoice {
    id: number;
    merchant_ref: string;
    tripay_reference: string | null;
    buyer_email: string;
    amount: number;
    payment_method: string | null;
    status: string;
    created_at: string;
    product: { id: number; name: string; sku: string } | null;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    invoices: Paginated<Invoice>;
    filters: { search: string; status: string };
    summary: { total: number; paid: number; unpaid: number; revenue: number };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Pesanan', href: '/admin/pesanan' },
];

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

function reload() {
    router.get('/admin/pesanan', { search: search.value, status: status.value }, { preserveState: true, replace: true });
}

watchDebounced(search, reload, { debounce: 350 });

function setStatus(value: string) {
    status.value = status.value === value ? '' : value;
    reload();
}

function rupiah(value: number) {
    return 'Rp' + value.toLocaleString('id-ID');
}

function formatDate(iso: string) {
    return new Date(iso).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

const statusStyle: Record<string, string> = {
    PAID: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
    UNPAID: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
    EXPIRED: 'bg-red-500/15 text-red-600 dark:text-red-400',
    FAILED: 'bg-red-500/15 text-red-600 dark:text-red-400',
    REFUND: 'bg-blue-500/15 text-blue-600 dark:text-blue-400',
};

const cards = [
    { label: 'Total Invoice', value: props.summary.total.toLocaleString('id-ID'), icon: Receipt },
    { label: 'Lunas', value: props.summary.paid.toLocaleString('id-ID'), icon: CheckCircle2 },
    { label: 'Menunggu', value: props.summary.unpaid.toLocaleString('id-ID'), icon: Clock },
    { label: 'Pendapatan (Lunas)', value: rupiah(props.summary.revenue), icon: Wallet },
];

const statusFilters = ['PAID', 'UNPAID', 'EXPIRED'];
</script>

<template>
    <Head title="Kelola Pesanan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Kelola Pesanan</h1>
                <p class="text-muted-foreground">Daftar invoice dari transaksi pembeli.</p>
            </div>

            <!-- Summary cards -->
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
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative max-w-sm flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Cari invoice, ref, atau email..." class="pl-9" />
                </div>
                <div class="flex gap-1">
                    <button
                        v-for="s in statusFilters"
                        :key="s"
                        class="rounded-full border px-3 py-1.5 text-xs font-medium transition-colors"
                        :class="status === s ? 'border-primary bg-primary text-primary-foreground' : 'border-border hover:bg-muted'"
                        @click="setStatus(s)"
                    >
                        {{ s }}
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                                <th class="px-5 py-3 font-medium">No. Invoice</th>
                                <th class="px-5 py-3 font-medium">Produk</th>
                                <th class="px-5 py-3 font-medium">Pembeli</th>
                                <th class="px-5 py-3 font-medium">Metode</th>
                                <th class="px-5 py-3 font-medium">Total</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                                <th class="px-5 py-3 font-medium">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="inv in invoices.data"
                                :key="inv.id"
                                class="cursor-pointer border-b border-sidebar-border/40 last:border-0 hover:bg-muted/40 dark:border-sidebar-border/40"
                                @click="router.get(`/admin/pesanan/${inv.id}`)"
                            >
                                <td class="px-5 py-3 font-mono text-xs">{{ inv.merchant_ref }}</td>
                                <td class="px-5 py-3">{{ inv.product?.name ?? '—' }}</td>
                                <td class="px-5 py-3 text-muted-foreground">{{ inv.buyer_email }}</td>
                                <td class="px-5 py-3">{{ inv.payment_method ?? '—' }}</td>
                                <td class="px-5 py-3 font-medium">{{ rupiah(inv.amount) }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusStyle[inv.status] ?? 'bg-muted text-muted-foreground'">
                                        {{ inv.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-muted-foreground">{{ formatDate(inv.created_at) }}</td>
                            </tr>

                            <tr v-if="invoices.data.length === 0">
                                <td colspan="7">
                                    <div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                            <Receipt class="h-6 w-6" />
                                        </span>
                                        <p class="font-medium">Belum ada invoice</p>
                                        <p class="max-w-xs text-sm text-muted-foreground">Invoice akan muncul di sini setelah pembeli melakukan checkout.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="invoices.total > 0" class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-muted-foreground">Menampilkan {{ invoices.from }}–{{ invoices.to }} dari {{ invoices.total }} invoice</p>
                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, i) in invoices.links" :key="i">
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
