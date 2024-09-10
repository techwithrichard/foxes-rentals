<div class="card-inner card-inner-lg">
    <div class="nk-block-head nk-block-head-lg">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">{{ __('Appearance settings')}}</h5>
                <span>{{ __('Customize look and feel,colors and appearance of whole web app.')}}</span>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content align-self-start d-lg-none">
                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                   data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
            </div>
        </div>
    </div><!-- .nk-block-head -->
    <div class="nk-block">
        <form class="form-settings" method="POST">
            <h5 class="title">{{ __('Layout & Appearance') }}</h5>

            <div>

                <!--Start main ui style mode-->
                <div class="row align-items-center">
                    <div class="col-md-5">

                        <div class="form-group">
                            <label class="form-label">{{ __('Main UI Style') }}</label>
                            <span
                                class="form-note">{{ __('Set skin color mode of your dashboard.') }}</span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="nk-opt-set">
                            <div class="nk-opt-set-title">{{__('Main UI Style')}}</div>
                            <div class="nk-opt-list col-2x">
                                <div wire:click="setMainUi('ui-default')" class="nk-opt-item only-text
                                                        {{ setting('main_ui_style')==='ui-default' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg"><span
                                                                class="nk-opt-item-name">Default</span></span></div>
                                <div wire:click="setMainUi('ui-clean')" class="nk-opt-item only-text
                                                        {{ setting('main_ui_style')==='ui-clean' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg"><span
                                                                class="nk-opt-item-name">Clean</span></span></div>
                                <div wire:click="setMainUi('ui-shady')" class="nk-opt-item only-text
                                                        {{ setting('main_ui_style')==='ui-shady' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg"><span
                                                                class="nk-opt-item-name">Shady</span></span></div>
                                <div wire:click="setMainUi('ui-softy')" class="nk-opt-item only-text
                                                        {{ setting('main_ui_style')==='ui-softy' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg"><span
                                                                class="nk-opt-item-name">Softy</span></span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End main ui style mode-->


                <!--Start primary skin mode-->
                <div class="row align-items-center">
                    <div class="col-md-5">

                        <div class="form-group">
                            <label class="form-label">{{ __('Primary skin') }}</label>
                            <span
                                class="form-note">{{ __('Set skin color mode of your dashboard.') }}</span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="nk-opt-set nk-opt-set-skin">
                            <div class="nk-opt-set-title">{{ __('Primary Skin')}}</div>
                            <div class="nk-opt-list">

                                @foreach($skin_colors as $item)
                                    <div wire:click="setSkinColor('{{ $item }}')"
                                         class="nk-opt-item {{ setting('skin_color')==$item ? 'active':'' }} ">
                                                        <span class="nk-opt-item-bg">
                                                            <span class="skin-{{ $item }}"></span>
                                                        </span>
                                        <span
                                            class="nk-opt-item-name">{{ Str::ucfirst($item) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!--End primary skin mode-->


                <!--Start skin mode-->
                <div class="row align-items-center">
                    <div class="col-md-5">

                        <div class="form-group">
                            <label class="form-label">{{ __('Skin Mode') }}</label>
                            <span
                                class="form-note">{{ __('Set skin color mode of your dashboard.') }}</span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="nk-opt-set">
                            <div class="nk-opt-set-title">{{ __('Skin Mode')}}</div>
                            <div class="nk-opt-list col-2x">
                                <div wire:click="setColorMode('light-mode')"
                                     class="nk-opt-item {{setting('color_mode_style')==='light-mode' ?'active':''}}">
                                                        <span class="nk-opt-item-bg is-light"><span
                                                                class="theme-light"></span></span><span
                                        class="nk-opt-item-name">{{ __('Light Skin')}}</span></div>
                                <div wire:click="setColorMode('dark-mode')"
                                     class="nk-opt-item {{setting('color_mode_style')==='dark-mode' ?'active':''}}">
                                                        <span class="nk-opt-item-bg"><span
                                                                class="theme-dark"></span></span><span
                                        class="nk-opt-item-name">{{ __('Dark Skin')}}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End skin mode-->

                <!--Start header color mode-->
                <div class="row align-items-center">
                    <div class="col-md-5">

                        <div class="form-group">
                            <label class="form-label">{{ __('Header Color Mode') }}</label>
                            <span
                                class="form-note">{{ __('Set header color mode of your dashboard.') }}</span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="nk-opt-set nk-opt-set-aside">
                            <div class="nk-opt-list col-4x">
                                <div wire:click="setHeaderStyle('is-light')"
                                     class="nk-opt-item {{ setting('header_color_style')==='is-light' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg is-light">
                                                            <span class="bg-lighter"></span>
                                                        </span>
                                    <span class="nk-opt-item-name">{{ __('White')}}</span>
                                </div>
                                <div wire:click="setHeaderStyle('is-default')"
                                     class="nk-opt-item {{ setting('header_color_style')==='is-default' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg is-light">
                                                            <span class="bg-light"></span>
                                                        </span>
                                    <span class="nk-opt-item-name">{{ __('Light')}}</span>
                                </div>
                                <div wire:click="setHeaderStyle('is-dark')"
                                     class="nk-opt-item {{ setting('header_color_style')==='is-dark' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg">
                                                            <span class="bg-dark"></span>
                                                        </span>
                                    <span class="nk-opt-item-name">{{ __('Dark')}}</span>
                                </div>
                                <div wire:click="setHeaderStyle('is-theme')"
                                     class="nk-opt-item {{ setting('header_color_style')==='is-theme' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg">
                                                            <span class="bg-theme"></span>
                                                        </span>
                                    <span class="nk-opt-item-name">{{ __('Theme')}}</span>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <!--End header color mode-->

                <!--Start sidebar color mode-->
                <div class="row align-items-center">
                    <div class="col-md-5">

                        <div class="form-group">
                            <label class="form-label">{{ __('Sidebar Color Mode') }}</label>
                            <span
                                class="form-note">{{ __('Set main sidebar color mode of your dashboard.') }}</span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="nk-opt-set nk-opt-set-aside">
                            <div class="nk-opt-list col-4x">
                                <div wire:click="setSidebarStyle('is-light')"
                                     class="nk-opt-item {{ setting('sidebar_color_style')==='is-light' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg is-light">
                                                            <span class="bg-lighter"></span>
                                                        </span>
                                    <span class="nk-opt-item-name">{{__('White')}}</span>
                                </div>
                                <div wire:click="setSidebarStyle('is-default')"
                                     class="nk-opt-item {{ setting('sidebar_color_style')==='is-default' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg is-light">
                                                            <span class="bg-light"></span>
                                                        </span>
                                    <span class="nk-opt-item-name">{{__('Light')}}</span>
                                </div>
                                <div wire:click="setSidebarStyle('is-dark')"
                                     class="nk-opt-item {{ setting('sidebar_color_style')==='is-dark' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg">
                                                            <span class="bg-dark"></span>
                                                        </span>
                                    <span class="nk-opt-item-name">{{__('Dark')}}</span>
                                </div>
                                <div wire:click="setSidebarStyle('is-theme')"
                                     class="nk-opt-item {{ setting('sidebar_color_style')==='is-theme' ? 'active':'' }}">
                                                        <span class="nk-opt-item-bg">
                                                            <span class="bg-theme"></span>
                                                        </span>
                                    <span class="nk-opt-item-name">{{__('Theme')}}</span>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <!--End sidebar color mode-->
            </div>
        </form>

    </div><!-- .nk-block-head -->
</div>
