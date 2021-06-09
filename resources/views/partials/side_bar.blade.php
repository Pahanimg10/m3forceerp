
<!-- START PAGE SIDEBAR -->
<div id="menuController" class="page-sidebar" ng-controller="menuController">
    <!-- START X-NAVIGATION -->
    <ul class="x-navigation">
        <li class="xn-logo">
            <a href="{{ asset('home') }}" style="text-transform: capitalize;">{{ session()->get('username') }}</a>
            <a href="#" class="x-navigation-control"></a>
        </li>
        <li class="xn-profile">
            <a href="#" class="profile-mini">
                <img src="{{ asset('assets/images/users/'.session()->get('user_image')) }}" alt="{{ session()->get('name') }}"/>
            </a>
            <div class="profile">
                <div class="profile-image">
                    <img src="{{ asset('assets/images/users/'.session()->get('user_image')) }}" alt="{{ session()->get('name') }}"/>
                </div>
                <div class="profile-data">
                    <div class="profile-data-name">{{ session()->get('name') }}</div>
                    <div class="profile-data-title">{{ session()->get('position') }}</div>
                </div>
                <div class="profile-controls">
                    <a href="{{ asset('dashboard/user_profile')}}" class="profile-control-left"><span class="fa fa-info"></span></a>
                    <a href="mailto:" class="profile-control-right"><span class="fa fa-envelope"></span></a>
                </div>
            </div>                                                                        
        </li>     
        
        @foreach ($main_menus as $main_menu)
            @if($main_menu->menu_url)
        <li id="{{ $main_menu->menu_id }}" class="">
            <a href="{{ asset($main_menu->menu_url) }}"><span class="{{ $main_menu->menu_icon }}"></span> <span class="xn-text">{{ $main_menu->menu_name }}</span></a>                        
        </li> 
            @else       
        <li id="{{ $main_menu->menu_id }}" class="xn-openable">
            <a href="#"><span class="{{ $main_menu->menu_icon }}"></span> <span class="xn-text">{{ $main_menu->menu_name }}</span></a>
            <ul>
                @foreach ($sub_menus as $sub_menu)
                    @if($main_menu->id == $sub_menu->menu_category)
                <li id="{{ $sub_menu->menu_id }}"><a href="{{ asset($sub_menu->menu_url) }}"><span class="{{ $sub_menu->menu_icon }}"></span>{{ $sub_menu->menu_name }}</a></li>
                    @endif
                @endforeach
            </ul>
        </li>
            @endif
        @endforeach

    </ul>
    <!-- END X-NAVIGATION -->
</div>
<!-- END PAGE SIDEBAR -->