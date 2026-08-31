<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, LoaderCircle } from 'lucide-vue-next';

interface Product {
    id: number;
    sku: string;
    name: string;
    price: number;
    reference: string | null;
}

const props = defineProps<{ product: Product }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/admin' },
    { title: 'Produk', href: '/admin/produk' },
    { title: 'Edit', href: `/admin/produk/${props.product.id}/edit` },
];

const form = useForm({
    sku: props.product.sku,
    name: props.product.name,
    price: props.product.price as number | null,
    reference: props.product.reference ?? '',
});

const submit = () => {
    form.transform((data) => ({ ...data, price: Number(data.price) })).put(`/admin/produk/${props.product.id}`);
};
</script>

<template>
    <Head title="Edit Produk" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div class="flex items-center gap-3">
                <Link href="/admin/produk">
                    <Button variant="outline" size="icon" class="h-9 w-9"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Edit Produk</h1>
                    <p class="text-muted-foreground">Perbarui detail produk <strong>{{ product.name }}</strong>.</p>
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
                        <Label for="reference">Merchant Reference (TriPay)</Label>
                        <Input id="reference" v-model="form.reference" placeholder="mis. REF-TRP-001 (opsional)" />
                        <p class="text-xs text-muted-foreground">Referensi merchant untuk integrasi pembayaran TriPay. Boleh dikosongkan.</p>
                        <InputError :message="form.errors.reference" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <Link href="/admin/produk"><Button type="button" variant="secondary">Batal</Button></Link>
                        <Button type="submit" :disabled="form.processing" class="gap-2">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Simpan Perubahan
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
