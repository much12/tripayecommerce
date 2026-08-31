<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, LoaderCircle } from 'lucide-vue-next';
import { onMounted, ref, onBeforeUnmount } from 'vue';

// CKEditor
import CKEditor from '@ckeditor/ckeditor5-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

// Dropzone
import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';

const ckeditor = CKEditor.component;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Produk', href: '/admin/produk' },
    { title: 'Tambah', href: '/admin/produk/create' },
];

const form = useForm({
    sku: '',
    name: '',
    price: null as number | null,
    reference: '',
    description: '',
    images: [] as File[],
});

const dropzoneRef = ref<HTMLElement | null>(null);
let dzInstance: Dropzone | null = null;

onMounted(() => {
    if (dropzoneRef.value) {
        dzInstance = new Dropzone(dropzoneRef.value, {
            url: "/", // Dummy URL as we don't use Dropzone's built-in upload
            autoProcessQueue: false,
            addRemoveLinks: true,
            acceptedFiles: "image/*",
            dictDefaultMessage: "Tarik & lepas gambar ke sini atau klik untuk memilih",
            dictRemoveFile: "Hapus",
        });

        dzInstance.on("addedfile", (file: any) => {
            form.images.push(file);
        });

        dzInstance.on("removedfile", (file: any) => {
            const index = form.images.findIndex((f) => f.name === file.name && f.size === file.size);
            if (index > -1) {
                form.images.splice(index, 1);
            }
        });
    }
});

onBeforeUnmount(() => {
    if (dzInstance) {
        dzInstance.destroy();
    }
});

const submit = () => {
    form.transform((data) => ({ ...data, price: Number(data.price) })).post('/admin/produk');
};
</script>

<template>
    <Head title="Tambah Produk" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div class="flex items-center gap-3">
                <Link href="/admin/produk">
                    <Button variant="outline" size="icon" class="h-9 w-9"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Tambah Produk</h1>
                    <p class="text-muted-foreground">Isi detail produk baru untuk katalog toko.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="max-w-2xl">
                <div class="grid gap-6 rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <div class="grid gap-2">
                        <Label for="sku">SKU <span class="text-red-500">*</span></Label>
                        <Input id="sku" v-model="form.sku" placeholder="mis. TRP-001" autofocus />
                        <p class="text-xs text-muted-foreground">Kode unik produk. Tidak boleh sama dengan produk lain.</p>
                        <InputError :message="form.errors.sku" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Nama Produk <span class="text-red-500">*</span></Label>
                        <Input id="name" v-model="form.name" placeholder="mis. Sepatu Sneakers Pria" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="price">Harga (Rp) <span class="text-red-500">*</span></Label>
                        <Input id="price" type="number" min="0" v-model="form.price" placeholder="mis. 349000" />
                        <InputError :message="form.errors.price" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Deskripsi Produk</Label>
                        <div class="prose max-w-none">
                            <ckeditor :editor="ClassicEditor" v-model="form.description" />
                        </div>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Gambar Produk</Label>
                        <div ref="dropzoneRef" class="dropzone rounded-md border-2 border-dashed bg-background hover:bg-muted/50 cursor-pointer"></div>
                        <p class="text-xs text-muted-foreground">Bisa memilih lebih dari 1 gambar.</p>
                        <InputError :message="form.errors.images" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="reference">Merchant Reference (TriPay)</Label>
                        <Input id="reference" v-model="form.reference" placeholder="mis. REF-TRP-001 (opsional)" />
                        <p class="text-xs text-muted-foreground">Referensi merchant untuk integrasi pembayaran TriPay. Boleh dikosongkan.</p>
                        <InputError :message="form.errors.reference" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <Link href="/admin/produk"><Button type="button" variant="secondary">Batal</Button></Link>
                        <Button type="submit" :disabled="form.processing" class="gap-2">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Simpan Produk
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style>
/* Adjust dropzone internal styling for dark mode or overall fit */
.dropzone {
    min-height: 150px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.dropzone .dz-message {
    margin: 2em 0;
}
.dropzone .dz-preview .dz-remove {
    cursor: pointer;
    margin-top: 0.5rem;
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    color: hsl(var(--destructive));
    border: 1px solid hsl(var(--destructive));
    border-radius: var(--radius);
    background: transparent;
    text-decoration: none;
}
.dropzone .dz-preview .dz-remove:hover {
    background: hsl(var(--destructive));
    color: hsl(var(--destructive-foreground));
}
/* CKEditor min-height */
.ck-editor__editable {
    min-height: 200px;
}
</style>
