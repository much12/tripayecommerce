<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { CheckCircle2, Package, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Product {
    id: number;
    sku: string;
    name: string;
    price: number;
    reference: string | null;
    created_at: string;
    category: { id: number; name: string } | null;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    products: Paginated<Product>;
    filters: { search: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Produk', href: '/admin/produk' },
];

const page = usePage();
const flashSuccess = computed(() => (page.props.flash as { success?: string })?.success);

const search = ref(props.filters.search ?? '');

watchDebounced(
    search,
    (value) => {
        router.get('/admin/produk', { search: value }, { preserveState: true, replace: true });
    },
    { debounce: 350 },
);

function rupiah(value: number) {
    return 'Rp' + value.toLocaleString('id-ID');
}

// Hapus produk
const deleteForm = useForm({});
const productToDelete = ref<Product | null>(null);

function confirmDelete() {
    if (!productToDelete.value) return;
    deleteForm.delete(`/admin/produk/${productToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => (productToDelete.value = null),
    });
}
</script>

<template>
    <Head title="Kelola Produk" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Kelola Produk</h1>
                    <p class="text-muted-foreground">Total {{ products.total }} produk dalam katalog.</p>
                </div>
                <Link href="/admin/produk/create">
                    <Button class="gap-2"><Plus class="h-4 w-4" /> Tambah Produk</Button>
                </Link>
            </div>

            <!-- Flash success -->
            <div
                v-if="flashSuccess"
                class="flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400"
            >
                <CheckCircle2 class="h-4 w-4" /> {{ flashSuccess }}
            </div>

            <!-- Search -->
            <div class="relative max-w-sm">
                <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="search" placeholder="Cari nama atau SKU..." class="pl-9" />
            </div>

            <!-- Table -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                                <th class="px-5 py-3 font-medium">SKU</th>
                                <th class="px-5 py-3 font-medium">Nama Produk</th>
                                <th class="px-5 py-3 font-medium">Kategori</th>
                                <th class="px-5 py-3 font-medium">Harga</th>
                                <th class="px-5 py-3 font-medium">Reference</th>
                                <th class="px-5 py-3 text-right font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="p in products.data"
                                :key="p.id"
                                class="border-b border-sidebar-border/40 last:border-0 hover:bg-muted/40 dark:border-sidebar-border/40"
                            >
                                <td class="px-5 py-3 font-mono text-xs">{{ p.sku }}</td>
                                <td class="px-5 py-3 font-medium">{{ p.name }}</td>
                                <td class="px-5 py-3">
                                    <span v-if="p.category" class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary">
                                        {{ p.category.name }}
                                    </span>
                                    <span v-else class="text-muted-foreground">—</span>
                                </td>
                                <td class="px-5 py-3">{{ rupiah(p.price) }}</td>
                                <td class="px-5 py-3 text-muted-foreground">{{ p.reference ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="`/admin/produk/${p.id}/edit`">
                                            <Button variant="ghost" size="icon" class="h-8 w-8" title="Edit">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-red-500 hover:text-red-600"
                                            title="Hapus"
                                            @click="productToDelete = p"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty state -->
                            <tr v-if="products.data.length === 0">
                                <td colspan="6">
                                    <div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                            <Package class="h-6 w-6" />
                                        </span>
                                        <p class="font-medium">Belum ada produk</p>
                                        <p class="max-w-xs text-sm text-muted-foreground">
                                            Mulai tambahkan produk pertamamu ke katalog toko.
                                        </p>
                                        <Link href="/admin/produk/create">
                                            <Button class="gap-2"><Plus class="h-4 w-4" /> Tambah Produk</Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="products.total > 0" class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-muted-foreground">
                    Menampilkan {{ products.from }}–{{ products.to }} dari {{ products.total }} produk
                </p>
                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, i) in products.links" :key="i">
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

        <!-- Dialog konfirmasi hapus -->
        <Dialog :open="productToDelete !== null" @update:open="(v) => { if (!v) productToDelete = null; }">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus produk?</DialogTitle>
                    <DialogDescription>
                        Produk <strong>{{ productToDelete?.name }}</strong> akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="secondary">Batal</Button>
                    </DialogClose>
                    <Button variant="destructive" :disabled="deleteForm.processing" @click="confirmDelete">Ya, Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
