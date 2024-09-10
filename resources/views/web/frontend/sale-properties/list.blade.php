properties for sale@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Properties for Sale</h1>
    <table class="table table-bordered" id="sale-properties-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sale-properties-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('/api/sale-properties') }}", // Update this if you have a different API endpoint
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'price', name: 'price' },
                { data: 'address', name: 'address' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
