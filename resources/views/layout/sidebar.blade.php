<!-- Sidebar  -->
<div class="iq-sidebar">
    <div class="iq-navbar-logo d-flex justify-content-between">
        <a href="index.html" class="header-logo" style="justify-content: center;">
            <img src="../assets/images/logo.png" class="img-fluid rounded" alt="">
        </a>
        <div class="iq-menu-bt align-self-center">
            <div class="wrapper-menu">
                <div class="main-circle"><i class="ri-menu-line"></i></div>
                <div class="hover-circle"><i class="ri-close-fill"></i></div>
            </div>
        </div>
    </div>
    <div id="sidebar-scrollbar">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                @php
                    use App\Models\Menu;
                    $menu = Menu::where('is_header', 'true')->orderBy('sort', 'ASC')->get();
                @endphp

                @foreach ($menu as $m)
                    @php
                        $menu_detail = Menu::where('parent', $m->id)->orderBy('sort', 'ASC')->get();
                    @endphp
                    <li class="{{ request()->is($m->url) ? 'active' : '' }}">
                        <a href="{{ $m->have_sub_menu == 'true' ? '#' . $m->url : '/' . $m->url }}"
                            class="iq-waves-effect {{ $m->have_sub_menu == 'true' ? 'collapsed' : '' }}"
                            {{ $m->have_sub_menu == 'true' ? 'data-toggle=collapse aria-expanded=false' : '' }}>
                            <span class="ripple rippleEffect"></span>
                            <i class="{{ $m->icon }} iq-arrow-left"></i>
                            <span>{!! wordwrap($m->name) !!}</span>
                            @if ($m->have_sub_menu == 'true')
                                <i class="ri-arrow-right-s-line iq-arrow-right"></i>
                            @endif
                        </a>
                        @if ($m->have_sub_menu == 'true')
                            <ul id="{{ $m->url }}" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                @foreach ($menu_detail as $item)
                                    <li class="{{ request()->is($item->url) ? 'active' : '' }}">
                                        <a href="/{{ $item->url }}">
                                            <i class="{{ $item->icon }}"></i>{!! wordwrap($item->name, 20, "<br>\n", true) !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>
