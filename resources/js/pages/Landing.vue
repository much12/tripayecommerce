<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Headphones,
    Heart,
    Menu,
    RotateCcw,
    Search,
    ShieldCheck,
    ShoppingBag,
    ShoppingCart,
    Star,
    Truck,
    X,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Product {
    id: number;
    sku: string;
    name: string;
    price: number;
    images: string[] | null;
}

defineProps<{
    products: Product[];
}>();

const mobileOpen = ref(false);

const categories = [
    { name: 'Fashion', emoji: '👕', count: '1.2rb produk' },
    { name: 'Elektronik', emoji: '📱', count: '860 produk' },
    { name: 'Rumah Tangga', emoji: '🏠', count: '540 produk' },
    { name: 'Kecantikan', emoji: '💄', count: '720 produk' },
    { name: 'Olahraga', emoji: '⚽', count: '410 produk' },
    { name: 'Hobi', emoji: '🎮', count: '390 produk' },
];

const features = [
    { icon: Truck, title: 'Gratis Ongkir', desc: 'Untuk pembelian di atas Rp200.000 ke seluruh Indonesia.' },
    { icon: ShieldCheck, title: 'Pembayaran Aman', desc: 'Transaksi terenkripsi dan terlindungi 100%.' },
    { icon: RotateCcw, title: 'Garansi 7 Hari', desc: 'Barang tidak sesuai? Kembalikan dengan mudah.' },
    { icon: Headphones, title: 'Dukungan 24/7', desc: 'Tim kami siap membantu kapan pun Anda butuh.' },
];

function rupiah(value: number) {
    return 'Rp' + value.toLocaleString('id-ID');
}

// Inisial produk sebagai placeholder gambar
function initials(name: string) {
    return name
        .split(' ')
        .slice(0, 2)
        .map((w) => w.charAt(0))
        .join('')
        .toUpperCase();
}
</script>

<template>
    <Head title="Tripay — Belanja Online Mudah & Aman">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-background text-foreground">
        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-border bg-background/80 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-4">
                <Link :href="route('home')" class="flex items-center gap-2 font-bold">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                        <ShoppingBag class="h-5 w-5" />
                    </span>
                    <span class="text-lg tracking-tight">Tripay</span>
                </Link>

                <!-- Search (desktop) -->
                <div class="relative ml-4 hidden flex-1 md:block">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <input
                        type="text"
                        placeholder="Cari produk, kategori, atau merek..."
                        class="h-10 w-full rounded-full border border-input bg-muted/40 pl-10 pr-4 text-sm outline-none focus:border-primary focus:bg-background"
                    />
                </div>

                <nav class="ml-auto hidden items-center gap-6 text-sm font-medium md:flex">
                    <a href="#kategori" class="text-muted-foreground transition-colors hover:text-foreground">Kategori</a>
                    <a href="#produk" class="text-muted-foreground transition-colors hover:text-foreground">Produk</a>
                </nav>

                <div class="ml-auto flex items-center gap-1 md:ml-0">
                    <button class="relative rounded-full p-2 hover:bg-accent" aria-label="Wishlist">
                        <Heart class="h-5 w-5" />
                    </button>
                    <button class="relative rounded-full p-2 hover:bg-accent" aria-label="Keranjang">
                        <ShoppingCart class="h-5 w-5" />
                        <span class="absolute right-0 top-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">3</span>
                    </button>

                    <Link
                        v-if="$page.props.auth.user"
                        :href="$page.props.auth.user.role === 'admin' ? route('admin.dashboard') : route('dashboard')"
                        class="ml-2 hidden sm:inline-flex"
                    >
                        <Button size="sm">Dashboard</Button>
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="ml-2 hidden sm:inline-flex">
                            <Button variant="ghost" size="sm">Masuk</Button>
                        </Link>
                        <Link :href="route('register')" class="hidden sm:inline-flex">
                            <Button size="sm">Daftar</Button>
                        </Link>
                    </template>

                    <button class="rounded-full p-2 hover:bg-accent md:hidden" aria-label="Menu" @click="mobileOpen = !mobileOpen">
                        <Menu v-if="!mobileOpen" class="h-5 w-5" />
                        <X v-else class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="mobileOpen" class="border-t border-border px-4 py-4 md:hidden">
                <div class="relative mb-4">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <input
                        type="text"
                        placeholder="Cari produk..."
                        class="h-10 w-full rounded-full border border-input bg-muted/40 pl-10 pr-4 text-sm outline-none focus:border-primary"
                    />
                </div>
                <nav class="flex flex-col gap-1 text-sm font-medium">
                    <a href="#kategori" class="rounded-md px-2 py-2 hover:bg-accent" @click="mobileOpen = false">Kategori</a>
                    <a href="#produk" class="rounded-md px-2 py-2 hover:bg-accent" @click="mobileOpen = false">Produk</a>
                </nav>
                <div class="mt-4 flex gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="$page.props.auth.user.role === 'admin' ? route('admin.dashboard') : route('dashboard')"
                        class="flex-1"
                    >
                        <Button class="w-full">Dashboard</Button>
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="flex-1"><Button variant="outline" class="w-full">Masuk</Button></Link>
                        <Link :href="route('register')" class="flex-1"><Button class="w-full">Daftar</Button></Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden border-b border-border">
            <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 lg:grid-cols-2 lg:py-24">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-border bg-muted/50 px-3 py-1 text-xs font-medium text-muted-foreground">
                        <Star class="h-3.5 w-3.5 fill-current text-amber-400" /> Dipercaya 50.000+ pembeli
                    </span>
                    <h1 class="mt-5 text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                        Belanja Online<br />
                        <span class="text-primary">Mudah, Cepat & Aman</span>
                    </h1>
                    <p class="mt-5 max-w-md text-base text-muted-foreground sm:text-lg">
                        Temukan ribuan produk pilihan dengan harga terbaik. Gratis ongkir, pembayaran aman, dan garansi kepuasan hanya di Tripay.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#produk">
                            <Button size="lg" class="gap-2">
                                Mulai Belanja <ArrowRight class="h-4 w-4" />
                            </Button>
                        </a>
                        <a href="#kategori">
                            <Button size="lg" variant="outline">Lihat Kategori</Button>
                        </a>
                    </div>
                    <div class="mt-10 flex gap-8">
                        <div>
                            <div class="text-2xl font-bold">10rb+</div>
                            <div class="text-sm text-muted-foreground">Produk</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">50rb+</div>
                            <div class="text-sm text-muted-foreground">Pelanggan</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold">4.9/5</div>
                            <div class="text-sm text-muted-foreground">Rating</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -right-10 -top-10 h-72 w-72 rounded-full bg-primary/10 blur-3xl"></div>
                    <div class="relative grid grid-cols-2 gap-4">
                        <div class="flex aspect-square items-center justify-center rounded-2xl border border-border bg-muted/40 text-6xl">👟</div>
                        <div class="mt-8 flex aspect-square items-center justify-center rounded-2xl border border-border bg-muted/40 text-6xl">🎧</div>
                        <div class="flex aspect-square items-center justify-center rounded-2xl border border-border bg-muted/40 text-6xl">⌚</div>
                        <div class="mt-8 flex aspect-square items-center justify-center rounded-2xl border border-border bg-muted/40 text-6xl">🎒</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="border-b border-border bg-muted/30">
            <div class="mx-auto grid max-w-7xl gap-6 px-4 py-10 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="f in features" :key="f.title" class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <component :is="f.icon" class="h-5 w-5" />
                    </div>
                    <div>
                        <h3 class="font-semibold">{{ f.title }}</h3>
                        <p class="mt-0.5 text-sm text-muted-foreground">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section id="kategori" class="mx-auto max-w-7xl px-4 py-16">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Kategori Populer</h2>
                    <p class="mt-1 text-muted-foreground">Jelajahi produk berdasarkan kategori favoritmu.</p>
                </div>
                <a href="#produk" class="hidden items-center gap-1 text-sm font-medium text-primary hover:underline sm:inline-flex">
                    Lihat semua <ArrowRight class="h-4 w-4" />
                </a>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                <a
                    v-for="c in categories"
                    :key="c.name"
                    href="#produk"
                    class="group flex flex-col items-center rounded-xl border border-border bg-card p-6 text-center transition-all hover:-translate-y-1 hover:border-primary hover:shadow-lg"
                >
                    <span class="text-4xl transition-transform group-hover:scale-110">{{ c.emoji }}</span>
                    <span class="mt-3 font-semibold">{{ c.name }}</span>
                    <span class="text-xs text-muted-foreground">{{ c.count }}</span>
                </a>
            </div>
        </section>

        <!-- Products -->
        <section id="produk" class="border-t border-border bg-muted/30">
            <div class="mx-auto max-w-7xl px-4 py-16">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Produk Unggulan</h2>
                        <p class="mt-1 text-muted-foreground">Pilihan terbaik dengan harga spesial minggu ini.</p>
                    </div>
                </div>
                <div v-if="products.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <div
                        v-for="p in products"
                        :key="p.id"
                        class="group flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-all hover:-translate-y-1 hover:shadow-lg"
                    >
                        <Link :href="route('product.show', p.sku)" class="relative flex aspect-square items-center justify-center bg-gradient-to-br from-muted to-muted/40">
                            <img v-if="p.images && p.images.length" :src="`/storage/${p.images[0]}`" :alt="p.name" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                            <span v-else class="text-4xl font-bold text-muted-foreground/50 transition-transform group-hover:scale-110">
                                {{ initials(p.name) }}
                            </span>
                            <button
                                class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-background/80 text-muted-foreground opacity-0 backdrop-blur transition-opacity hover:text-red-500 group-hover:opacity-100"
                                aria-label="Tambah ke wishlist"
                            >
                                <Heart class="h-4 w-4" />
                            </button>
                        </Link>
                        <div class="flex flex-1 flex-col p-4">
                            <Link :href="route('product.show', p.sku)">
                                <h3 class="line-clamp-2 text-sm font-medium hover:text-primary">{{ p.name }}</h3>
                            </Link>
                            <div class="mt-1 font-mono text-xs text-muted-foreground">{{ p.sku }}</div>
                            <div class="mt-3 flex items-end gap-2">
                                <span class="font-bold text-primary">{{ rupiah(p.price) }}</span>
                            </div>
                            <Link :href="`/checkout/${p.id}`" class="mt-3">
                                <Button size="sm" class="w-full gap-2">
                                    <ShoppingCart class="h-4 w-4" /> Beli Sekarang
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div
                    v-else
                    class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-border py-20 text-center"
                >
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                        <ShoppingBag class="h-7 w-7" />
                    </span>
                    <p class="font-medium">Belum ada produk</p>
                    <p class="max-w-xs text-sm text-muted-foreground">Produk akan tampil di sini setelah admin menambahkannya.</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-border bg-muted/30">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <div class="flex items-center gap-2 font-bold">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                                <ShoppingBag class="h-4 w-4" />
                            </span>
                            <span>Tripay</span>
                        </div>
                        <p class="mt-3 text-sm text-muted-foreground">Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik.</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">Belanja</h4>
                        <ul class="mt-3 space-y-2 text-sm text-muted-foreground">
                            <li><a href="#kategori" class="hover:text-foreground">Kategori</a></li>
                            <li><a href="#produk" class="hover:text-foreground">Produk Unggulan</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">Bantuan</h4>
                        <ul class="mt-3 space-y-2 text-sm text-muted-foreground">
                            <li><a href="#" class="hover:text-foreground">Cara Belanja</a></li>
                            <li><a href="#" class="hover:text-foreground">Pengiriman</a></li>
                            <li><a href="#" class="hover:text-foreground">Pengembalian</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">Tentang</h4>
                        <ul class="mt-3 space-y-2 text-sm text-muted-foreground">
                            <li><a href="#" class="hover:text-foreground">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-foreground">Kontak</a></li>
                            <li><a href="#" class="hover:text-foreground">Kebijakan Privasi</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-10 border-t border-border pt-6 text-center text-sm text-muted-foreground">
                    © 2026 Tripay. Semua hak dilindungi.
                </div>
            </div>
        </footer>
    </div>
</template>
