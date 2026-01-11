<div class="container section">
    <div class="card">
        <div class="card-content">
            <div class="row mb-0">
                <div class="col s12 m6">
                    <span class="card-title">
                        <i class="material-icons left">history</i>
                        Registro de Auditoría
                    </span>
                </div>
                <div class="col s12 m6 right-align">
                    <button wire:click="clearFilters" class="btn waves-effect waves-light grey">
                        <i class="material-icons left">clear_all</i>
                        Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="row">
                <div class="input-field col s12 m4">
                    <i class="material-icons prefix">search</i>
                    <input wire:model.live.debounce.300ms="search" id="search_audit" type="text">
                    <label for="search_audit">Buscar...</label>
                </div>
                
                <div class="col s12 m4">
                    <label>Filtrar por Evento</label>
                    <select wire:model.live="filterEvent" class="browser-default" style="display: block; width: 100%; padding: 5px; border: 1px solid #ddd; margin-top: 5px;">
                        <option value="">Todos los eventos</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}">{{ ucfirst($event) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col s12 m4">
                    <label>Filtrar por Modelo</label>
                    <select wire:model.live="filterModel" class="browser-default" style="display: block; width: 100%; padding: 5px; border: 1px solid #ddd; margin-top: 5px;">
                        <option value="">Todos los modelos</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}">{{ class_basename($model) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Audit Timeline -->
            <div class="row">
                <div class="col s12">
                    @forelse($audits as $audit)
                        <div class="card-panel hoverable" style="margin-bottom: 15px; border-left: 4px solid {{ $audit->event === 'created' ? '#4caf50' : ($audit->event === 'updated' ? '#2196f3' : '#f44336') }};">
                            <div class="row mb-0">
                                <div class="col s12 m8">
                                    <h6 class="mb-0">
                                        @if($audit->event === 'created')
                                            <span class="new badge green" data-badge-caption="">Creado</span>
                                        @elseif($audit->event === 'updated')
                                            <span class="new badge blue" data-badge-caption="">Actualizado</span>
                                        @elseif($audit->event === 'deleted')
                                            <span class="new badge red" data-badge-caption="">Eliminado</span>
                                        @else
                                            <span class="new badge grey" data-badge-caption="">{{ ucfirst($audit->event) }}</span>
                                        @endif
                                        
                                        <strong>{{ class_basename($audit->auditable_type) }}</strong>
                                        @if($audit->auditable_id)
                                            <span class="grey-text">#{{ $audit->auditable_id }}</span>
                                        @endif
                                    </h6>
                                    
                                    <p class="grey-text text-darken-1 mb-0">
                                        <i class="material-icons tiny">person</i>
                                        {{ $audit->user ? $audit->user->name : 'Sistema' }}
                                        <i class="material-icons tiny" style="margin-left: 15px;">access_time</i>
                                        {{ $audit->created_at->format('d/m/Y H:i:s') }}
                                        <span class="grey-text text-lighten-1">({{ $audit->created_at->diffForHumans() }})</span>
                                    </p>
                                </div>
                                <div class="col s12 m4 right-align">
                                    <button class="btn-small waves-effect waves-light indigo" 
                                            onclick="toggleDetails{{ $audit->id }}()"
                                            id="toggleBtn{{ $audit->id }}">
                                        <i class="material-icons">expand_more</i>
                                    </button>
                                </div>
                            </div>

                            <!-- Details (collapsible) -->
                            <div id="details{{ $audit->id }}" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                @if($audit->event === 'created' && $audit->new_values)
                                    <h6 class="green-text"><i class="material-icons tiny">add_circle</i> Valores Creados:</h6>
                                    <div class="grey lighten-4" style="padding: 10px; border-radius: 4px; font-family: monospace; font-size: 0.9em;">
                                        @foreach($audit->new_values as $key => $value)
                                            <div><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($audit->event === 'updated' && ($audit->old_values || $audit->new_values))
                                    <div class="row">
                                        @if($audit->old_values)
                                            <div class="col s12 m6">
                                                <h6 class="red-text"><i class="material-icons tiny">remove_circle</i> Valores Anteriores:</h6>
                                                <div class="red lighten-5" style="padding: 10px; border-radius: 4px; font-family: monospace; font-size: 0.9em;">
                                                    @foreach($audit->old_values as $key => $value)
                                                        <div><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($audit->new_values)
                                            <div class="col s12 m6">
                                                <h6 class="green-text"><i class="material-icons tiny">add_circle</i> Valores Nuevos:</h6>
                                                <div class="green lighten-5" style="padding: 10px; border-radius: 4px; font-family: monospace; font-size: 0.9em;">
                                                    @foreach($audit->new_values as $key => $value)
                                                        <div><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($audit->event === 'deleted' && $audit->old_values)
                                    <h6 class="red-text"><i class="material-icons tiny">delete</i> Valores Eliminados:</h6>
                                    <div class="red lighten-4" style="padding: 10px; border-radius: 4px; font-family: monospace; font-size: 0.9em;">
                                        @foreach($audit->old_values as $key => $value)
                                            <div><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                <div style="margin-top: 10px;">
                                    <small class="grey-text">
                                        <i class="material-icons tiny">computer</i> IP: {{ $audit->ip_address ?? 'N/A' }}
                                        <i class="material-icons tiny" style="margin-left: 10px;">devices</i> User Agent: {{ Str::limit($audit->user_agent ?? 'N/A', 50) }}
                                    </small>
                                </div>
                            </div>

                            <script>
                                function toggleDetails{{ $audit->id }}() {
                                    const details = document.getElementById('details{{ $audit->id }}');
                                    const btn = document.getElementById('toggleBtn{{ $audit->id }}');
                                    if (details.style.display === 'none') {
                                        details.style.display = 'block';
                                        btn.innerHTML = '<i class="material-icons">expand_less</i>';
                                    } else {
                                        details.style.display = 'none';
                                        btn.innerHTML = '<i class="material-icons">expand_more</i>';
                                    }
                                }
                            </script>
                        </div>
                    @empty
                        <div class="card-panel center-align grey-text">
                            <i class="material-icons large">inbox</i>
                            <p>No hay registros de auditoría disponibles.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $audits->links() }}
            </div>
        </div>
    </div>
</div>
