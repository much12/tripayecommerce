<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar produk.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/products/Index', [
            'products' => $products,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Form tambah produk.
     */
    public function create(): Response
    {
        return Inertia::render('admin/products/Create');
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('admin/products/Edit', [
            'product' => $product,
        ]);
    }

    /**
     * Perbarui produk.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateData($request, $product);

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Aturan validasi produk.
     *
     * @return array<string, mixed>
     */
    protected function validateData(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'reference' => ['nullable', 'string', 'max:255'],
        ], [], [
            'sku' => 'SKU',
            'name' => 'nama produk',
            'price' => 'harga',
            'reference' => 'merchant reference',
        ]);
    }
}
