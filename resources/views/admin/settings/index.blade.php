@extends('layouts.app')

@section('content')
<div class="container container-custom py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ __('Database Management & Settings') }}</h2>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-speedometer2 me-1"></i> {{ __('Admin Dashboard') }}
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Reset Data') }}</h4>
                            <p class="card-text">{{ __('Manage database tables') }}</p>
                        </div>
                        <i class="bi bi-trash" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Backup') }}</h4>
                            <p class="card-text">{{ __('Create database backup') }}</p>
                        </div>
                        <i class="bi bi-hdd" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Restore') }}</h4>
                            <p class="card-text">{{ __('Restore from backup') }}</p>
                        </div>
                        <i class="bi bi-arrow-repeat" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">{{ __('Backups') }}</h4>
                            <p class="card-text">{{ __('Manage backup files') }}</p>
                        </div>
                        <i class="bi bi-folder" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Tables Management -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ __('Database Tables') }}</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <strong>{{ __('Warning:') }}</strong> {{ __('Resetting a table will permanently delete all data in that table. This action cannot be undone. Please exercise caution.') }}
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Table Name') }}</th>
                            <th>{{ __('Row Count') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tableData as $table)
                        <tr>
                            <td>{{ $table['name'] }}</td>
                            <td>
                                @if($table['count'] > 0)
                                    <span class="badge bg-primary">{{ $table['count'] }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $table['count'] }}</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="resetTable('{{ $table['name'] }}')" 
                                        {{ $table['count'] == 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash me-1"></i> {{ __('Reset') }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                {{ __('No database tables found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Backup Management -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Create Backup') }}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ __('Create a backup of your current database. This will save all data to a file that can be used for restoration.') }}</p>
                    <button class="btn btn-success" onclick="createBackup()">
                        <i class="bi bi-hdd me-1"></i> {{ __('Create New Backup') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Backup Files') }}</h5>
                </div>
                <div class="card-body">
                    @if(!empty($backupFiles))
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Filename') }}</th>
                                    <th>{{ __('Size') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backupFiles as $backup)
                                <tr>
                                    <td>{{ $backup['name'] }}</td>
                                    <td>{{ number_format($backup['size']/1024, 2) }} KB</td>
                                    <td>{{ $backup['modified'] }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1" onclick="restoreBackup('{{ $backup['name'] }}')">
                                            <i class="bi bi-arrow-repeat me-1"></i> {{ __('Restore') }}
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup('{{ $backup['name'] }}')">
                                            <i class="bi bi-trash me-1"></i> {{ __('Delete') }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">{{ __('No backup files found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for confirmation -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmAction">{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let actionType = '';
    let tableName = '';
    let backupFilename = '';

    function resetTable(table) {
        actionType = 'reset';
        tableName = table;
        
        document.getElementById('modalTitle').textContent = '{{ __('Reset Table Confirmation') }}';
        document.getElementById('modalMessage').innerHTML =
            '{{ __('Are you sure you want to reset the table') }} <strong>' + table + '</strong>?<br>' +
            '{{ __('This will permanently delete all data in this table. This action cannot be undone.') }}';

        // Store the action parameters
        actionType = 'reset';
        tableName = table;

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        modal.show();

        // Set up the confirm button to execute the action
        document.getElementById('confirmAction').onclick = function() {
            performReset();
        };
    }

    function createBackup() {
        document.getElementById('modalTitle').textContent = '{{ __('Create Backup Confirmation') }}';
        document.getElementById('modalMessage').textContent = '{{ __('Are you sure you want to create a new database backup? This may take a moment to complete.') }}';

        // Store the action parameters
        actionType = 'backup';

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        modal.show();

        // Set up the confirm button to execute the action
        document.getElementById('confirmAction').onclick = function() {
            performBackup();
        };
    }

    function restoreBackup(filename) {
        document.getElementById('modalTitle').textContent = '{{ __('Restore Backup Confirmation') }}';
        document.getElementById('modalMessage').innerHTML =
            '{{ __('Are you sure you want to restore from backup') }} <strong>' + filename + '</strong>?<br>' +
            '{{ __('This will overwrite all current data in the database. This action cannot be undone.') }}';

        // Store the action parameters
        actionType = 'restore';
        backupFilename = filename;

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        modal.show();

        // Set up the confirm button to execute the action
        document.getElementById('confirmAction').onclick = function() {
            performRestore();
        };
    }

    function deleteBackup(filename) {
        document.getElementById('modalTitle').textContent = '{{ __('Delete Backup Confirmation') }}';
        document.getElementById('modalMessage').innerHTML =
            '{{ __('Are you sure you want to delete backup') }} <strong>' + filename + '</strong>?<br>' +
            '{{ __('This action cannot be undone.') }}';

        // Store the action parameters
        actionType = 'delete';
        backupFilename = filename;

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        modal.show();

        // Set up the confirm button to execute the action
        document.getElementById('confirmAction').onclick = function() {
            performDelete();
        };
    }


    function performReset() {
        fetch('{{ route("admin.settings.reset-table") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                table_name: tableName
            })
        })
        .then(response => response.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            
            if(data.success) {
                alert(data.message);
                location.reload(); // Reload to update row counts
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            alert('Error: ' + error);
        });
    }

    function performBackup() {
        fetch('{{ route("admin.settings.backup") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            
            if(data.success) {
                alert(data.message);
                location.reload(); // Reload to see the new backup file
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            alert('Error: ' + error);
        });
    }

    function performRestore() {
        fetch('{{ route("admin.settings.restore") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                filename: backupFilename
            })
        })
        .then(response => response.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            
            if(data.success) {
                alert(data.message);
                // Don't reload immediately as restore takes time
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            alert('Error: ' + error);
        });
    }

    function performDelete() {
        fetch('{{ route("admin.settings.delete-backup") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                filename: backupFilename
            })
        })
        .then(response => response.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            
            if(data.success) {
                alert(data.message);
                location.reload(); // Reload to update backup list
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
            alert('Error: ' + error);
        });
    }
</script>
@endpush
@endsection