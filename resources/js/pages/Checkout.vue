<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, LoaderCircle, ShieldCheck, ShoppingBag } from 'lucide-vue-next';

interface Product {
    id: number;
    sku: string;
    name: string;
    price: number;
}

interface Channel {
    code: string;
    name: string;
    group: string;
    icon_url: string | null;
}

const props = defineProps<{
    product: Product;
    channels: Channel[];
}>();

const form = useForm({
    product_id: props.product.id,
    buyer_email: '',
    buyer_phone: '',
    method: '',
});

function rupiah(value: number) {
    return 'Rp' + value.toLocaleString('id-ID');
}

const submit = () => {
    form.post('/checkout');
};
</script>

<template>
    <Head title="Checkout" />

    <div class="min-h-screen bg-background text-foreground">
        <!-- Header -->
        <header class="border-b border-border">
            <div class="mx-auto flex max-w-5xl items-center gap-3 px-4 py-4">
                <Link href="/" class="flex items-center gap-2 font-bold">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                        <ShoppingBag class="h-5 w-5" />
                    </span>
                    <span class="text-lg tracking-tight">Tripay</span>
                </Link>
                <span class="ml-2 text-sm text-muted-foreground">Checkout</span>
            </div>
        </header>

        <div class="mx-auto max-w-5xl px-4 py-8">
            <Link href="/" class="mb-6 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="h-4 w-4" /> Kembali ke toko
            </Link>

            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
                <!-- Kolom form -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Data pembeli -->
                    <div class="rounded-xl border border-border bg-card p-6">
                        <h2 class="mb-4 font-semibold">Data Pembeli</h2>
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="buyer_email">Email <span class="text-red-500">*</span></Label>
                                <Input id="buyer_email" type="email" v-model="form.buyer_email" placeholder="email@anda.com" />
                                <InputError :message="form.errors.buyer_email" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="buyer_phone">Nomor HP <span class="text-red-500">*</span></Label>
                                <Input id="buyer_phone" type="tel" v-model="form.buyer_phone" placeholder="081234567890" />
                                <InputError :message="form.errors.buyer_phone" />
                            </div>
                        </div>
                    </div>

                    <!-- Metode pembayaran -->
                    <div class="rounded-xl border border-border bg-card p-6">
                        <h2 class="mb-4 font-semibold">Metode Pembayaran</h2>
                        <InputError :message="form.errors.method" class="mb-3" />

                        <div v-if="channels.length" class="grid gap-2 sm:grid-cols-2">
                            <label
                                v-for="c in channels"
                                :key="c.code"
                                class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors"
                                :class="form.method === c.code ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-border hover:bg-muted/50'"
                            >
                                <input type="radio" class="sr-only" :value="c.code" v-model="form.method" />
                                <img v-if="c.icon_url" :src="c.icon_url" :alt="c.name" class="h-6 w-10 object-contain" />
                                <span v-else class="flex h-6 w-10 items-center justify-center rounded bg-muted text-[10px] font-bold">{{ c.code }}</span>
                                <span class="text-sm font-medium">{{ c.name }}</span>
                            </label>
                        </div>

                        <div v-else class="rounded-lg border border-dashed border-border p-4 text-sm text-muted-foreground">
                            Tidak ada channel pembayaran yang tersedia. Pastikan konfigurasi TriPay sudah benar.
                        </div>
                    </div>
                </div>

                <!-- Ringkasan pesanan -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-4 rounded-xl border border-border bg-card p-6">
                        <h2 class="font-semibold">Ringkasan Pesanan</h2>
                        <div class="flex gap-3">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-muted text-sm font-bold text-muted-foreground">
                                {{ product.name.slice(0, 2).toUpperCase() }}
                            </span>
                            <div>
                                <p class="text-sm font-medium leading-tight">{{ product.name }}</p>
                                <p class="font-mono text-xs text-muted-foreground">{{ product.sku }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 border-t border-border pt-4 text-sm">
                            <div class="flex justify-between text-muted-foreground">
                                <span>Subtotal</span>
                                <span>{{ rupiah(product.price) }}</span>
                            </div>
                            <div class="flex justify-between text-muted-foreground">
                                <span>Biaya admin</span>
                                <span>Ditentukan channel</span>
                            </div>
                            <div class="flex justify-between border-t border-border pt-2 text-base font-bold">
                                <span>Total</span>
                                <span class="text-primary">{{ rupiah(product.price) }}</span>
                            </div>
                        </div>

                        <Button type="submit" class="w-full gap-2" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Bayar Sekarang
                        </Button>

                        <p class="flex items-center justify-center gap-1.5 text-xs text-muted-foreground">
                            <ShieldCheck class="h-3.5 w-3.5" /> Pembayaran aman via TriPay
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
