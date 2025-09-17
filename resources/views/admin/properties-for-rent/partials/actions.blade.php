<ul class="nk-tb-actions gx-1">
    <li>
        <div class="drodown">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                <em class="icon ni ni-more-h"></em>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <ul class="link-list-opt no-bdr">
                    <li>
                        <a href="{{ route('admin.properties.show', $property->id) }}">
                            <em class="icon ni ni-eye"></em>
                            <span>{{ __('View Details') }}</span>
                        </a>
                    </li>
                    
                    @can('edit property')
                        <li>
                            <a href="{{ route('admin.properties.edit', $property->id) }}">
                                <em class="icon ni ni-edit"></em>
                                <span>{{ __('Edit Property') }}</span>
                            </a>
                        </li>
                    @endcan
                    
                    @if($property->is_vacant)
                        <li>
                            <a href="{{ route('admin.leases.create') }}?property_id={{ $property->id }}">
                                <em class="icon ni ni-plus"></em>
                                <span>{{ __('Create Lease') }}</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('admin.leases.index') }}?property_id={{ $property->id }}">
                                <em class="icon ni ni-eye"></em>
                                <span>{{ __('View Lease') }}</span>
                            </a>
                        </li>
                    @endif
                    
                    <li>
                        <a href="#" onclick="markAsFeatured({{ $property->id }})">
                            <em class="icon ni ni-star"></em>
                            <span>{{ __('Mark as Featured') }}</span>
                        </a>
                    </li>
                    
                    @can('delete property')
                        <li class="divider"></li>
                        <li>
                            <a href="#" data-id="{{ $property->id }}" class="delete-item text-danger">
                                <em class="icon ni ni-delete"></em>
                                <span class="text-danger">{{ __('Delete Property') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        </div>
    </li>
</ul>

<script>
function markAsFeatured(propertyId) {
    // Implement mark as featured functionality
    alert('Mark as featured functionality will be implemented');
}
</script>
