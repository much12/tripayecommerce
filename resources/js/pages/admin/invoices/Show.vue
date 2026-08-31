<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ExternalLink } from 'lucide-vue-next';

interface Invoice {
    id: number;
    merchant_ref: string;
    tripay_reference: string | null;
    buyer_email: string;
    buyer_phone: string;
    amount: number;
    payment_method: string | null;
    checkout_url: string | null;
    status: string;
    paid_at: string | null;
    created_at: string;
    raw_response: Record<string, unknown> | null;
    product: { id: number; name: string; sku: string; price: number } | null;
}

const props = defineProps<{ invoice: Invoice }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Pesanan', href: '/admin/pesanan' },
    { title: props.invoice.merchant_ref, href: `/admin/pesanan/${props.invoice.id}` },
];

function rupiah(value: number) {
    return 'Rp' + value.toLocaleString('id-ID');
}

function formatDate(iso: string | null) {
    if (!iso) return '—';
    return new Date(iso).toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

const statusStyle: Record<string, string> = {
    PAID: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
    UNPAID: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
    EXPIRED: 'bg-red-500/15 text-red-600 dark:text-red-400',
    FAILED: 'bg-red-500/15 text-red-600 dark:text-red-400',
    REFUND: 'bg-blue-500/15 text-blue-600 dark:text-blue-400',
};
</script>

<template>
    <Head :title="`Invoice ${invoice.merchant_ref}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link href="/admin/pesanan">
                        <Button variant="outline" size="icon" class="h-9 w-9"><ArrowLeft class="h-4 w-4" /></Button>
                    </Link>
                    <div>
                        <h1 class="font-mono text-xl font-bold tracking-tight">{{ invoice.merchant_ref }}</h1>
                        <p class="text-sm text-muted-foreground">Dibuat {{ formatDate(invoice.created_at) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="rounded-full px-3 py-1 text-sm font-semibold" :class="statusStyle[invoice.status] ?? 'bg-muted text-muted-foreground'">
                        {{ invoice.status }}
                    </span>
                    <a v-if="invoice.checkout_url" :href="invoice.checkout_url" target="_blank" rel="noopener noreferrer">
                        <Button variant="outline" class="gap-2"><ExternalLink class="h-4 w-4" /> Halaman Bayar</Button>
                    </a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Detail utama -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Produk -->
                    <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                        <h2 class="mb-4 font-semibold">Produk</h2>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-muted text-sm font-bold text-muted-foreground">
                                    {{ (invoice.product?.name ?? '?').slice(0, 2).toUpperCase() }}
                                </span>
                                <div>
                                    <p class="font-medium">{{ invoice.product?.name ?? 'Produk dihapus' }}</p>
                                    <p class="font-mono text-xs text-muted-foreground">{{ invoice.product?.sku ?? '—' }}</p>
                                </div>
                            </div>
                            <span class="font-semibold">{{ rupiah(invoice.amount) }}</span>
                        </div>
                    </div>

                    <!-- Raw response -->
                    <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                        <h2 class="mb-4 font-semibold">Respons API TriPay</h2>
                        <pre class="max-h-96 overflow-auto rounded-lg bg-muted/50 p-4 text-xs leading-relaxed">{{ JSON.stringify(invoice.raw_response, null, 2) }}</pre>
                    </div>
                </div>

                <!-- Sidebar info -->
                <div class="space-y-6">
                    <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                        <h2 class="mb-4 font-semibold">Pembayaran</h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-muted-foreground">Total</dt>
                                <dd class="font-semibold">{{ rupiah(invoice.amount) }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-muted-foreground">Metode</dt>
                                <dd>{{ invoice.payment_method ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-muted-foreground">Ref TriPay</dt>
                                <dd class="break-all text-right font-mono text-xs">{{ invoice.tripay_reference ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-muted-foreground">Dibayar pada</dt>
                                <dd class="text-right">{{ formatDate(invoice.paid_at) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                        <h2 class="mb-4 font-semibold">Pembeli</h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-muted-foreground">Email</dt>
                                <dd class="break-all text-right">{{ invoice.buyer_email }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-muted-foreground">No. HP</dt>
                                <dd>{{ invoice.buyer_phone }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
