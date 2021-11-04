<!-- start header section -->
<section class="header-section">
    <nav class="navbar navbar-expand-lg navbar-light nav-bg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{url('account/'.$prefix.'/dashboard')}}">
                <img src="{{asset('Modules/Core/Public/assets/images/logo_icon.png')}}" alt="" width="30" height="24"> Shujog
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-lg-0">
                    @if(auth()->guard('web')->check())
                        @php
                          $modules = \Modules\Core\Models\Auth\ModuleUser::with('module')->where(['user_id'=>auth()->user()->id])->get();
                          $submoduleIds = \Modules\Core\Models\Auth\SubmoduleUser::where(['user_id'=>auth()->user()->id])->pluck('submodule_id')->toArray();
                        @endphp

                        @foreach($modules as $mod)
                            @php
                                $submodules = $mod->module->submodules->whereIn('id',$submoduleIds);
                            @endphp
                            @if(count($submodules) > 0)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown{{$mod->module->id}}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{$mod->module->title}}
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown{{$mod->module->id}}">
                                        @foreach($submodules as $sub)
                                            <li>
                                                @if($sub->action_type == "url")
                                                    <a class="dropdown-item" href="{{url($sub->action)}}"> @if($sub->icons != "") {!! $sub->icons !!} @elseif($sub->upload_icon != "") <img src="{{asset('public/uploads/submodule-icon/'.$sub->upload_icon)}}" alt="{{$sub->title}} Icon" height="20px"> @endif {{$sub->title}}</a>
                                                @else
                                                    <a class="dropdown-item" href="{{route($sub->action,\Illuminate\Support\Facades\Cookie::get('prefix'))}}"> @if($sub->icons != "") {!! $sub->icons !!} @elseif($sub->upload_icon != "") <img src="{{asset('public/uploads/submodule-icon/'.$sub->upload_icon)}}" alt="{{$sub->title}} Icon" height="20px"> @endif {{$sub->title}}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                            <li class="nav-item">
                                @if($mod->action_type == "url")
                                    <a class="nav-link" aria-current="page" href="{{url($mod->action)}}"> @if($mod->icons != "") {!! $mod->icons !!} @else <img src="{{asset('public/uploads/submodule-icon/'.$mod->upload_icon)}}" alt="{{$mod->title}} Icon" height="20px"> @endif {{$mod->module->title}}</a>
                                @else
                                    <a class="nav-link" aria-current="page" href="{{route($mod->action,\Illuminate\Support\Facades\Cookie::get('prefix'))}}"> @if($mod->icons != "") {!! $mod->icons !!} @else <img src="{{asset('public/uploads/module-icon/'.$mod->upload_icon)}}" alt="{{$mod->title}} Icon" height="20px"> @endif {{$mod->module->title}}</a>
                                @endif
                            </li>
                            @endif
                        @endforeach

                    @endif



                </ul>

                <div class="d-flex">
                    <ul class="navbar-nav me-auto mb-lg-0">

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarLanguage" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <!--<span><i class="fas fa-angle-down"></i></span>-->
                                @if(Session::has('locale'))
                                    @if(Session::get('locale') == 'en')
                                        English
                                    @else
                                        Bangla
                                    @endif
                                @else
                                    Bangla
                                @endif
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarLanguage">
                                <li><a class="dropdown-item" href="{{url('language/en')}}">English</a></li>
                                <li><a class="dropdown-item" href="{{url('language/bn')}}">Bangla</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown setting-header-container">
                            <a class="nav-link dropdown-toggle setting-link" href="#" id="navbarSetting" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-circle" style="background-color: #fff">
                                    @if(Auth::guard('web')->user()->self_picture != "")
                                        <img
                                            src="{{asset('public/users_image/'.Auth::guard('web')->user()->self_picture)}}" alt="User Icon">
                                    @else
                                        <img src="{{asset('Modules/Core/Public/assets/images/663328.png')}}" alt="User Icon"/>
                                    @endif
                                </div>
                                <span>
                                     @php
                                         $users_info = App\Models\RetailNetwork\RetailNetworkUser::where(['id'=>Auth::guard('web')->user()->id])->first();
                                           if(!is_null($users_info)){
                                               echo $users_info->name;
                                           }
                                     @endphp
                                </span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end header-right-submenu" aria-labelledby="navbarSetting">
                                @if(auth()->user()->is_system_admin == 1)
                                    <li><a class="dropdown-item right-submenu-header" href="#"><img src="{{asset('Modules/ManualOrder/Public/images/icons/check-mark-icon.png')}}" alt=""/> Settings</a></li>
                                    <li style="margin-left: 30px;margin-top: 10px;"><a class="dropdown-item" href="{{url('account/'.$prefix.'/settings/modules')}}"><img src="{{asset('Modules/ManualOrder/Public/images/icons/pattern-icon.png')}}" alt=""/> Modules</a></li>
                                    <li style="margin-left: 30px;margin-top: 10px;"><a class="dropdown-item" href="{{url('account/'.$prefix.'/settings/components')}}"><img src="{{asset('Modules/ManualOrder/Public/images/icons/pattern-icon.png')}}" alt=""/> Components</a></li>
                                    <li style="margin-left: 30px;margin-top: 10px;"><a class="dropdown-item" href="{{url('account/'.$prefix.'/settings/permissions')}}"><img src="{{asset('Modules/ManualOrder/Public/images/icons/pattern-icon.png')}}" alt=""/> Permissions</a></li>
                                    <li style="margin-left: 30px;margin-top: 10px;"><a class="dropdown-item" href="{{url('account/'.$prefix.'/settings/roles')}}"><img src="{{asset('Modules/ManualOrder/Public/images/icons/pattern-icon.png')}}" alt=""/> Roles</a></li>
                                    <li style="margin-left: 30px;margin-top: 10px;"><a class="dropdown-item" href="{{url('account/'.$prefix.'/settings/users')}}"><img src="{{asset('Modules/ManualOrder/Public/images/icons/pattern-icon.png')}}" alt=""/> Users</a></li>
                                @endif
                                <li style="margin-top: 10px;"><a class="dropdown-item" href="{{url('auth/sujog/logout')}}"><i class="fas fa-power-off"></i> Logout</a></li>
                            </ul>

                        </li>


                    </ul>
                </div>

            </div>

        </div>
    </nav>
</section>
<!-- end header section -->
