 <x-app-layout>
    <x-slot name="importedLinks">
        @include('includes.datatables-links')
    </x-slot>
    <x-slot name="pageTitle">
        Sensors
    </x-slot>
    <x-slot name="content">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="left w-50">
                            <h3 class="card-title">Database Backup</h3>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="defaultTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>File Name</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($backups as $index => $backup)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $backup['name'] }}</td>
                                        <td>{{ $backup['formatted_date'] }}</td>
                                        <td>
                                            <a href="{{ route('dbbackup.download', ['name' => $backup['name']]) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i>
                                                Download
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No backup files found.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
    @section('scripts')
        @include('includes.datatables-scripts')
        <script src="{{ asset('assets/js/datatables.js') }}"></script>
        <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('./assets/js/sweetalert-delete.js') }}"></script>
    @endsection
</x-app-layout>
