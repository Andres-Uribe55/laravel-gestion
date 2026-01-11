<div class="container section">
    <div class="card">
        <div class="card-content">
            <div class="row mb-0">
                <div class="col s12 m6">
                    <span class="card-title">Gestión de Productos</span>
                </div>
                <div class="col s12 m6 right-align">
                    <button wire:click="create" class="btn waves-effect waves-light indigo">
                        + Nuevo Producto
                    </button>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="card-panel green lighten-4 green-text text-darken-4">
                    <strong>¡Éxito!</strong> {{ session('message') }}
                </div>
            @endif

            <div class="row">
                <div class="input-field col s12">
                    <i class="material-icons prefix">search</i>
                    <input wire:model.live.debounce.300ms="search" id="search" type="text">
                    <label for="search">Buscar productos...</label>
                </div>
            </div>

            <div class="responsive-table">
                <table class="striped highlight">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th class="center-align">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="circle responsive-img" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="circle grey lighten-2 valign-wrapper center-align" style="width: 50px; height: 50px; color: #9e9e9e;">N/A</div>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ Str::limit($product->description, 50) }}</td>
                                <td class="green-text text-darken-2 font-bold">${{ number_format($product->price, 2) }}</td>
                                <td class="center-align">
                                    <button wire:click="edit({{ $product->id }})" class="btn-small flat waves-effect waves-teal">
                                        <i class="material-icons text-indigo-600">edit</i>
                                    </button>
                                    <button wire:click="confirmDeletion({{ $product->id }})" class="btn-small flat waves-effect waves-red">
                                        <i class="material-icons red-text">delete</i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="center-align grey-text">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
        <div class="modal open" style="display: block; background-color: rgba(0,0,0,0.5); z-index: 1003; top: 0; width: 100%; height: 100%; max-height: 100%;">
            <div class="modal-content white" style="margin: 10% auto; padding: 24px; border-radius: 4px; max-width: 600px; position: relative;">
                <h4>{{ $productId ? 'Editar Producto' : 'Crear Producto' }}</h4>
                
                <div class="row">
                    <div class="input-field col s12">
                        <input wire:model="name" id="name" type="text" class="validate">
                        <label for="name" class="{{ $name ? 'active' : '' }}">Nombre</label>
                        @error('name') <span class="red-text small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <textarea wire:model="description" id="description" class="materialize-textarea"></textarea>
                        <label for="description" class="{{ $description ? 'active' : '' }}">Descripción</label>
                        @error('description') <span class="red-text small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <input wire:model="price" id="price" type="number" step="0.01" class="validate">
                        <label for="price" class="{{ $price ? 'active' : '' }}">Precio</label>
                        @error('price') <span class="red-text small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="file-field input-field">
                    <div class="btn indigo">
                        <span>Imagen</span>
                        <input type="file" wire:model.live="image" accept="image/*">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                    <div wire:loading wire:target="image" class="progress indigo lighten-4" style="margin-top: 10px;">
                        <div class="indeterminate indigo"></div>
                    </div>
                    @error('image') <span class="red-text small">{{ $message }}</span> @enderror
                    @if ($image)
                        <div class="mt-2">
                            <span class="grey-text text-lighten-1" style="font-size: 0.8rem">Vista previa:</span>
                            <img src="{{ $image->temporaryUrl() }}" class="responsive-img rounded" style="max-height: 100px;">
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="modal-close waves-effect waves-red btn-flat">Cancelar</button>
                    <button wire:click="store" class="waves-effect waves-light btn indigo">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($confirmingProductDeletion)
        <div class="modal open" style="display: block; background-color: rgba(0,0,0,0.5); z-index: 1003; top: 0; width: 100%; height: 100%; max-height: 100%;">
            <div class="modal-content white" style="margin: 15% auto; padding: 24px; border-radius: 4px; max-width: 500px; position: relative;">
                <div class="center-align">
                    <i class="material-icons red-text large">warning</i>
                    <h4>Eliminar Producto</h4>
                    <p>¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer center-align" style="text-align: center;">
                    <button wire:click="$set('confirmingProductDeletion', false)" class="modal-close waves-effect waves-green btn-flat">Cancelar</button>
                    <button wire:click="delete" class="waves-effect waves-light btn red">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
