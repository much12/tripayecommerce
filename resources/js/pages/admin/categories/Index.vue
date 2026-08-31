<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { Edit2, Plus, Search, Tag, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Category {
    id: number;
    name: string;
    slug: string;
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
    categories: Paginated<Category>;
    filters: { search: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Kategori', href: '/admin/kategori' },
];

const search = ref(props.filters.search ?? '');

function reload() {
    router.get('/admin/kategori', { search: search.value }, { preserveState: true, replace: true });
}

watchDebounced(search, reload, { debounce: 350 });

function formatDate(iso: string) {
    return new Date(iso).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

// Modal state
const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const isDeleteOpen = ref(false);
const selectedCategory = ref<Category | null>(null);

const createForm = useForm({
    name: '',
});

const editForm = useForm({
    name: '',
});

function openEdit(category: Category) {
    selectedCategory.value = category;
    editForm.name = category.name;
    isEditOpen.value = true;
}

function openDelete(category: Category) {
    selectedCategory.value = category;
    isDeleteOpen.value = true;
}

function submitCreate() {
    createForm.post(route('admin.categories.store'), {
        onSuccess: () => {
            isCreateOpen.value = false;
            createForm.reset();
        },
    });
}

function submitEdit() {
    if (!selectedCategory.value) return;
    editForm.put(route('admin.categories.update', selectedCategory.value.id), {
        onSuccess: () => {
            isEditOpen.value = false;
            editForm.reset();
        },
    });
}

function submitDelete() {
    if (!selectedCategory.value) return;
    router.delete(route('admin.categories.destroy', selectedCategory.value.id), {
        onSuccess: () => {
            isDeleteOpen.value = false;
        },
    });
}
</script>

<template>
    <Head title="Kelola Kategori" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Kelola Kategori</h1>
                    <p class="text-muted-foreground">Kelola kategori produk untuk toko.</p>
                </div>
                
                <Dialog v-model:open="isCreateOpen">
                    <DialogTrigger as-child>
                        <Button class="gap-2">
                            <Plus class="h-4 w-4" /> Tambah Kategori
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Tambah Kategori</DialogTitle>
                            <DialogDescription>Masukkan nama kategori baru untuk produk.</DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="submitCreate" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="name">Nama Kategori</Label>
                                <Input id="name" v-model="createForm.name" placeholder="Misal: Pakaian Pria" />
                                <p v-if="createForm.errors.name" class="text-sm text-red-500">{{ createForm.errors.name }}</p>
                            </div>
                            <DialogFooter>
                                <Button type="submit" :disabled="createForm.processing">Simpan</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative max-w-sm flex-1">
                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" placeholder="Cari nama kategori..." class="pl-9" />
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-sidebar-border/70 text-left text-muted-foreground dark:border-sidebar-border">
                                <th class="px-5 py-3 font-medium">ID</th>
                                <th class="px-5 py-3 font-medium">Nama Kategori</th>
                                <th class="px-5 py-3 font-medium">Slug</th>
                                <th class="px-5 py-3 font-medium">Dibuat Pada</th>
                                <th class="px-5 py-3 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="category in categories.data"
                                :key="category.id"
                                class="border-b border-sidebar-border/40 last:border-0 hover:bg-muted/40 dark:border-sidebar-border/40"
                            >
                                <td class="px-5 py-3 font-medium">{{ category.id }}</td>
                                <td class="px-5 py-3 font-medium">{{ category.name }}</td>
                                <td class="px-5 py-3 text-muted-foreground">{{ category.slug }}</td>
                                <td class="px-5 py-3 text-xs text-muted-foreground">{{ formatDate(category.created_at) }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button variant="ghost" size="icon" @click="openEdit(category)" title="Edit">
                                            <Edit2 class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon" class="text-red-500 hover:text-red-600" @click="openDelete(category)" title="Hapus">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="categories.data.length === 0">
                                <td colspan="5">
                                    <div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                            <Tag class="h-6 w-6" />
                                        </span>
                                        <p class="font-medium">Tidak ada kategori</p>
                                        <p class="max-w-xs text-sm text-muted-foreground">Belum ada kategori yang ditambahkan atau sesuai dengan pencarian.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="categories.total > 0" class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-muted-foreground">Menampilkan {{ categories.from }}–{{ categories.to }} dari {{ categories.total }} kategori</p>
                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, i) in categories.links" :key="i">
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

        <!-- Edit Modal -->
        <Dialog v-model:open="isEditOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit Kategori</DialogTitle>
                    <DialogDescription>Ubah nama kategori yang sudah ada.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="edit-name">Nama Kategori</Label>
                        <Input id="edit-name" v-model="editForm.name" />
                        <p v-if="editForm.errors.name" class="text-sm text-red-500">{{ editForm.errors.name }}</p>
                    </div>
                    <DialogFooter>
                        <Button type="submit" :disabled="editForm.processing">Simpan Perubahan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Modal -->
        <Dialog v-model:open="isDeleteOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Kategori</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus kategori <strong>{{ selectedCategory?.name }}</strong>? 
                        Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2 sm:gap-0">
                    <Button variant="outline" @click="isDeleteOpen = false">Batal</Button>
                    <Button variant="destructive" @click="submitDelete">Ya, Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>