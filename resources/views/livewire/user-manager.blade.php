<div class="container section">
    <div class="card">
        <div class="card-content">
            <div class="row mb-0">
                <div class="col s12 m6">
                    <span class="card-title">Gestión de Usuarios</span>
                </div>
                <div class="col s12 m6 right-align">
                    <button wire:click="create" class="btn waves-effect waves-light indigo">
                         + Nuevo Usuario
                    </button>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="card-panel green lighten-4 green-text text-darken-4">
                    <strong>¡Éxito!</strong> {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="card-panel red lighten-4 red-text text-darken-4">
                    <strong>¡Error!</strong> {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="input-field col s12">
                     <i class="material-icons prefix">search</i>
                    <input wire:model.live.debounce.300ms="search" id="search_users" type="text">
                    <label for="search_users">Buscar usuarios...</label>
                </div>
            </div>

            <div class="responsive-table">
                <table class="striped highlight">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Creado</th>
                            <th class="center-align">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->roles->isNotEmpty())
                                        <span class="new badge indigo" data-badge-caption="">{{ $user->roles->first()->name }}</span>
                                    @else
                                        <span class="new badge grey" data-badge-caption="">Sin Rol</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                                <td class="center-align">
                                    <button wire:click="edit({{ $user->id }})" class="btn-small flat waves-effect waves-teal">
                                        <i class="material-icons text-indigo-600">edit</i>
                                    </button>
                                    <button wire:click="confirmDeletion({{ $user->id }})" class="btn-small flat waves-effect waves-red" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <i class="material-icons red-text">delete</i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="center-align grey-text">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
        <div class="modal open" style="display: block; background-color: rgba(0,0,0,0.5); z-index: 1003; top: 0; width: 100%; height: 100%; max-height: 100%;">
            <div class="modal-content white" style="margin: 10% auto; padding: 24px; border-radius: 4px; max-width: 600px; position: relative;">
                <h4>{{ $userId ? 'Editar Usuario' : 'Crear Usuario' }}</h4>
                
                <div class="row">
                    <div class="input-field col s12">
                        <input wire:model="name" id="user_name" type="text" class="validate">
                        <label for="user_name" class="{{ $name ? 'active' : '' }}">Nombre</label>
                        @error('name') <span class="red-text small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <input wire:model="email" id="user_email" type="email" class="validate">
                        <label for="user_email" class="{{ $email ? 'active' : '' }}">Correo Electrónico</label>
                        @error('email') <span class="red-text small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                         <input wire:model="password" id="user_password" type="password" class="validate">
                        <label for="user_password">Contraseña {{ $userId ? '(Dejar en blanco para mantener)' : '' }}</label>
                        @error('password') <span class="red-text small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col s12">
                        <label>Rol de Usuario</label>
                        <select wire:model="role" class="browser-default" style="display: block; width: 100%; padding: 5px; border: 1px solid #ddd; background-color: #fce4ec00; margin-top: 5px;">
                            <option value="">Seleccione un rol</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="red-text small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="modal-close waves-effect waves-red btn-flat">Cancelar</button>
                    <button wire:click="store" class="waves-effect waves-light btn indigo">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($confirmingUserDeletion)
        <div class="modal open" style="display: block; background-color: rgba(0,0,0,0.5); z-index: 1003; top: 0; width: 100%; height: 100%; max-height: 100%;">
            <div class="modal-content white" style="margin: 15% auto; padding: 24px; border-radius: 4px; max-width: 500px; position: relative;">
                <div class="center-align">
                    <i class="material-icons red-text large">warning</i>
                    <h4>Eliminar Usuario</h4>
                    <p>¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer center-align" style="text-align: center;">
                    <button wire:click="$set('confirmingUserDeletion', false)" class="modal-close waves-effect waves-green btn-flat">Cancelar</button>
                    <button wire:click="delete" class="waves-effect waves-light btn red">Eliminar</button>
                </div>
            </div>
        </div>
    @endif
</div>
