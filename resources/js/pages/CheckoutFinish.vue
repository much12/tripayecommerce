<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, Clock, ShoppingBag, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';

interface Invoice {
    merchant_ref: string;
    tripay_reference: string | null;
    amount: number;
    status: string;
    buyer_email: string;
}

const props = defineProps<{
    invoice: Invoice | null;
}>();

function rupiah(value: number) {
    return 'Rp' + value.toLocaleString('id-ID');
}

const view = computed(() => {
    const status = props.invoice?.status ?? 'UNKNOWN';
    if (status === 'PAID') {
        return { icon: CheckCircle2, color: 'text-emerald-500', title: 'Pembayaran Berhasil', desc: 'Terima kasih! Pembayaran Anda telah kami terima.' };
    }
    if (status === 'EXPIRED' || status === 'FAILED') {
        return { icon: XCircle, color: 'text-red-500', title: 'Pembayaran Gagal', desc: 'Transaksi kedaluwarsa atau dibatalkan. Silakan coba lagi.' };
    }
    return { icon: Clock, color: 'text-amber-500', title: 'Menunggu Pembayaran', desc: 'Selesaikan pembayaran Anda sesuai instruksi dari TriPay.' };
});
</script>

<template>
    <Head title="Status Pembayaran" />

    <div class="flex min-h-screen items-center justify-center bg-background px-4 text-foreground">
        <div class="w-full max-w-md rounded-2xl border border-border bg-card p-8 text-center">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-muted" :class="view.color">
                <component :is="view.icon" class="h-8 w-8" />
            </span>
            <h1 class="mt-5 text-xl font-bold">{{ view.title }}</h1>
            <p class="mt-2 text-sm text-muted-foreground">{{ view.desc }}</p>

            <div v-if="invoice" class="mt-6 space-y-2 rounded-xl border border-border bg-muted/30 p-4 text-left text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">No. Invoice</span>
                    <span class="font-mono">{{ invoice.merchant_ref }}</span>
                </div>
                <div v-if="invoice.tripay_reference" class="flex justify-between">
                    <span class="text-muted-foreground">Ref TriPay</span>
                    <span class="font-mono">{{ invoice.tripay_reference }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Total</span>
                    <span class="font-semibold">{{ rupiah(invoice.amount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Status</span>
                    <span class="font-semibold">{{ invoice.status }}</span>
                </div>
            </div>

            <Link href="/" class="mt-6 inline-block w-full">
                <Button class="w-full gap-2"><ShoppingBag class="h-4 w-4" /> Kembali ke Toko</Button>
            </Link>
        </div>
    </div>
</template>
