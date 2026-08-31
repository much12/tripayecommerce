<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, ShieldCheck, ShoppingCart } from 'lucide-vue-next';
import { ref } from 'vue';

interface Product {
    id: number;
    sku: string;
    name: string;
    price: number;
    description: string | null;
    images: string[] | null;
}

const props = defineProps<{
    product: Product;
}>();

const mainImage = ref(props.product.images && props.product.images.length > 0 ? props.product.images[0] : null);

function rupiah(value: number) {
    return 'Rp' + value.toLocaleString('id-ID');
}
</script>

<template>
    <Head :title="`${product.name} - Tripay`" />

    <div class="min-h-screen bg-background text-foreground">
        <!-- Header -->
        <header class="border-b border-border bg-background">
            <div class="mx-auto flex max-w-7xl items-center px-4 py-4">
                <Link :href="route('home')" class="flex items-center gap-2 text-sm font-medium hover:text-primary">
                    <ArrowLeft class="h-4 w-4" /> Kembali ke Beranda
                </Link>
            </div>
        </header>

        <!-- Product Detail Section -->
        <main class="mx-auto max-w-7xl px-4 py-8 sm:py-12">
            <div class="grid gap-10 md:grid-cols-2">
                
                <!-- Images -->
                <div class="space-y-4">
                    <div class="aspect-square overflow-hidden rounded-2xl border border-border bg-muted/30">
                        <img v-if="mainImage" :src="`/storage/${mainImage}`" :alt="product.name" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center text-4xl text-muted-foreground/50">
                            No Image
                        </div>
                    </div>
                    
                    <div v-if="product.images && product.images.length > 1" class="flex gap-4 overflow-x-auto pb-2">
                        <button 
                            v-for="(img, idx) in product.images" 
                            :key="idx"
                            @click="mainImage = img"
                            class="h-20 w-20 shrink-0 overflow-hidden rounded-lg border-2"
                            :class="mainImage === img ? 'border-primary' : 'border-transparent hover:border-primary/50'"
                        >
                            <img :src="`/storage/${img}`" class="h-full w-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex flex-col">
                    <div class="mb-2 font-mono text-sm text-muted-foreground">SKU: {{ product.sku }}</div>
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ product.name }}</h1>
                    
                    <div class="mt-4 text-3xl font-bold text-primary">{{ rupiah(product.price) }}</div>
                    
                    <div class="mt-6 flex flex-col gap-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-700 dark:text-emerald-400">
                        <div class="flex items-center gap-2">
                            <CheckCircle2 class="h-4 w-4" /> Stok Tersedia
                        </div>
                        <div class="flex items-center gap-2">
                            <ShieldCheck class="h-4 w-4" /> 100% Produk Original & Bergaransi
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        <h3 class="text-lg font-semibold">Deskripsi Produk</h3>
                        <div v-if="product.description" class="prose max-w-none text-muted-foreground dark:prose-invert" v-html="product.description"></div>
                        <p v-else class="text-muted-foreground">Belum ada deskripsi untuk produk ini.</p>
                    </div>

                    <div class="mt-10 flex gap-4">
                        <Link :href="`/checkout/${product.id}`" class="flex-1">
                            <Button size="lg" class="w-full gap-2 text-base">
                                <ShoppingCart class="h-5 w-5" /> Beli Sekarang
                            </Button>
                        </Link>
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>
