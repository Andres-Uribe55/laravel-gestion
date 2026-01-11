<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;

class AuditLog extends Component
{
    use WithPagination;

    public $search = '';
    public $filterEvent = '';
    public $filterModel = '';

    protected $queryString = ['search', 'filterEvent', 'filterModel'];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403, 'No tiene permiso para acceder a los registros de auditoría.');
        }
    }

    public function render()
    {
        $query = Audit::with(['user'])
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('auditable_type', 'like', '%' . $this->search . '%')
                  ->orWhere('event', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Event filter
        if ($this->filterEvent) {
            $query->where('event', $this->filterEvent);
        }

        // Model filter
        if ($this->filterModel) {
            $query->where('auditable_type', $this->filterModel);
        }

        $audits = $query->paginate(15);

        // Get unique events and models for filters
        $events = Audit::distinct()->pluck('event');
        $models = Audit::distinct()->pluck('auditable_type');

        return view('livewire.audit-log', [
            'audits' => $audits,
            'events' => $events,
            'models' => $models,
        ])->layout('layouts.app');
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterEvent', 'filterModel']);
    }
}
