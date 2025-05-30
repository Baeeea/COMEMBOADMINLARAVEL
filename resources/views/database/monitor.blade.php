@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Database Monitoring Dashboard</h4>
                    <div>
                        <form action="{{ route('db.snapshot.create') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Take Snapshot</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info" role="alert">
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="row">
                        <!-- Database Info Card -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Database Information</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Database Name
                                            <span class="badge bg-primary rounded-pill">{{ $dbInfo['name'] }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Driver
                                            <span class="badge bg-secondary rounded-pill">{{ $dbInfo['driver'] }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Version
                                            <span class="badge bg-info rounded-pill">{{ $dbInfo['version'] }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Tables Count
                                            <span class="badge bg-success rounded-pill">{{ $dbInfo['tables'] }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Table Status Card -->
                        <div class="col-md-8 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header {{ count($messagesStatus['issues']) > 0 ? 'bg-warning' : 'bg-success' }} text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Messages Table Status</h5>
                                    @if(count($messagesStatus['issues']) > 0)
                                        <form action="{{ route('db.messages.fix') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light">Fix Issues</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if(!$messagesStatus['exists'])
                                        <div class="alert alert-danger">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Messages table does not exist!
                                        </div>
                                    @else
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Table Structure</h6>
                                                <ul class="list-group mb-3">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        Messages Count
                                                        <span class="badge bg-info rounded-pill">{{ $messagesStatus['row_count'] }}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        sender_type Column
                                                        @if($messagesStatus['has_sender_type'])
                                                            <span class="badge bg-success">Present</span>
                                                        @else
                                                            <span class="badge bg-danger">Missing</span>
                                                        @endif
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        receiver_type Column
                                                        @if($messagesStatus['has_receiver_type'])
                                                            <span class="badge bg-success">Present</span>
                                                        @else
                                                            <span class="badge bg-danger">Missing</span>
                                                        @endif
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Data Issues</h6>
                                                @if(count($messagesStatus['issues']) > 0)
                                                    <div class="alert alert-warning">
                                                        <ul class="mb-0">
                                                            @foreach($messagesStatus['issues'] as $issue)
                                                                <li>{{ $issue }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <div class="alert alert-success">
                                                        <i class="bi bi-check-circle-fill"></i> No issues detected
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Snapshots List -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Database Snapshots</h5>
                                </div>
                                <div class="card-body">
                                    @if(count($snapshots) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Tables</th>
                                                        <th>Database</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($snapshots as $snapshot)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($snapshot['created_at'])->format('M d, Y H:i') }}</td>
                                                            <td>{{ $snapshot['tables'] }}</td>
                                                            <td>{{ $snapshot['database'] }}</td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('db.snapshot.view', $snapshot['id']) }}" class="btn btn-info btn-sm">
                                                                        <i class="bi bi-eye"></i>
                                                                    </a>
                                                                    <form action="{{ route('db.snapshot.compare') }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="snapshot_id" value="{{ $snapshot['id'] }}">
                                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                                            <i class="bi bi-arrow-left-right"></i>
                                                                        </button>
                                                                    </form>
                                                                    <form action="{{ route('db.snapshot.delete', $snapshot['id']) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this snapshot?')">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No snapshots available. Click "Take Snapshot" to create your first database snapshot.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Changelog -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Database Change Log</h5>
                                </div>
                                <div class="card-body">
                                    @if(count($changelog) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Action</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($changelog as $entry)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($entry['timestamp'])->format('M d, Y H:i') }}</td>
                                                            <td>
                                                                @switch($entry['type'])
                                                                    @case('snapshot')
                                                                        <span class="badge bg-success">Snapshot</span>
                                                                        @break
                                                                    @case('comparison')
                                                                        <span class="badge bg-primary">Comparison</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge bg-secondary">{{ ucfirst($entry['type']) }}</span>
                                                                @endswitch
                                                            </td>
                                                            <td>{{ $entry['description'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No changelog entries available yet.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
