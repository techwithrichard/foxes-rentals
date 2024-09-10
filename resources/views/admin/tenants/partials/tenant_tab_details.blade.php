<div class="nk-block nk-block-between">
    <div class="nk-block-head">
        <h6 class="title">{{ __('Personal Information')}}</h6>
        <p>{{ __('Tenant personal information.')}}</p>
    </div><!-- .nk-block-head -->

    @can('edit tenant')


        <div class="nk-block">
            <a href="{{ route('admin.tenants.edit',$tenant->id)}}"
               class="btn btn-white btn-icon btn-outline-light">
                <em class="icon ni ni-edit"></em>
            </a>
        </div>

    @endcan
</div><!-- .nk-block-between  -->

<div class="nk-block">

    <div class="profile-ud-list">

        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Full Name')}}</span>
                <span class="profile-ud-value">{{ $tenant->name}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span
                    class="profile-ud-label">{{ __('Email Address')}}</span>
                <span class="profile-ud-value">{{ $tenant->email}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span
                    class="profile-ud-label">{{ __('Phone Number')}}</span>
                <span class="profile-ud-value">{{ $tenant->phone}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('ID Number')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->identity_no}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span
                    class="profile-ud-label">{{ __('Occupation Status')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->occupation_status}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span
                    class="profile-ud-label">{{ __('Occupation Place')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->occupation_place}}</span>
            </div>
        </div>

        @if($tenant->identity_document)
            <div class="profile-ud-item">
                <div class="profile-ud wider">
                <span
                    class="profile-ud-label">{{ __('Identity Document')}}</span>
                    <span
                        class="profile-ud-value">
                        <a href="{{ url($tenant->identity_document) }}"
                           download
                           target="_blank">
                            <em class="icon ni ni-download"></em>
                            {{ __('View Document')}}
                        </a>
                    </span>
                </div>
            </div>
        @endif

    </div><!-- .profile-ud-list -->
</div><!-- .nk-block -->

<div class="nk-block">
    <div class="nk-block-head nk-block-head-line">
        <h6 class="title overline-title text-base">
            {{ __('Emergency Contact Person')}}
        </h6>
    </div><!-- .nk-block-head -->
    <div class="profile-ud-list">
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Name')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->emergency_name}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Email')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->emergency_email}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Phone')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->emergency_contact}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span
                    class="profile-ud-label">{{ __('Relationship')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->emergency_relationship}}</span>
            </div>
        </div>
    </div><!-- .profile-ud-list -->
</div><!-- .nk-block -->

<div class="nk-block">
    <div class="nk-block-head nk-block-head-line">
        <h6 class="title overline-title text-base">
            {{__('Next Of Kin Information')}}
        </h6>
    </div><!-- .nk-block-head -->
    <div class="profile-ud-list">
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Name')}}</span>
                <span class="profile-ud-value">{{ $tenant->kin_name}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('ID Number')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->kin_identity}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span class="profile-ud-label">{{ __('Phone')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->kin_phone}}</span>
            </div>
        </div>
        <div class="profile-ud-item">
            <div class="profile-ud wider">
                <span
                    class="profile-ud-label">{{ __('Relationship')}}</span>
                <span
                    class="profile-ud-value">{{ $tenant->kin_relationship}}</span>
            </div>
        </div>
    </div><!-- .profile-ud-list -->
</div><!-- .nk-block -->
