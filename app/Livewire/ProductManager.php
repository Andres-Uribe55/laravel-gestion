<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Product;
use App\Services\ProductImageService;

class ProductManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name, $description, $price, $image;
    public $productId;
    public $isModalOpen = false;
    public $confirmingProductDeletion = false;  
    public $search = '';

    protected $rules = [
        'name' => 'required|string|min:3',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|max:10240', // 10MB Max
    ];

    public function render()
    {
        $products = Product::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.product-manager', [
            'products' => $products,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->image = null;
        $this->productId = null;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
        ];

        // If editing and updating image, or creating with image
        if ($this->image) {
            // Check if we are editing and verify previous image to delete it
            if ($this->productId) {
                $product = Product::find($this->productId);
                if ($product && $product->image_path) {
                    app(ProductImageService::class)->delete($product->image_path);
                }
            }

            // Upload new image
            $path = app(ProductImageService::class)->upload($this->image);
            $data['image_path'] = $path;
        }

        Product::updateOrCreate(['id' => $this->productId], $data);

        session()->flash('message', $this->productId ? 'Producto actualizado exitosamente.' : 'Producto creado exitosamente.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        // Image handling is tricky for edit, we don't prepopulate the file input
    
        $this->openModal();
    }

    public function confirmDeletion($id)
    {
        $this->confirmingProductDeletion = $id;
    }

    public function delete()
    {
        $product = Product::find($this->confirmingProductDeletion);
        if ($product) {
            // Delete image if exists
            if ($product->image_path) {
                app(ProductImageService::class)->delete($product->image_path);
            }

            $product->delete();
            session()->flash('message', 'Producto eliminado exitosamente.');
        }
        $this->confirmingProductDeletion = false;
    }
}
