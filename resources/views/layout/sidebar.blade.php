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
                    use App\Models\RoleMenuAccess;

                    $user = auth()->user();
                    $roleCode = $user ? $user->role_code : null;

                    // Get accessible menu IDs for the current user's role
$accessibleMenuIds = collect();
if ($roleCode) {
    // Super Admin has access to all menus
    if ($roleCode === 'SUPERADMIN') {
        $accessibleMenuIds = Menu::pluck('id');
    } else {
        $accessibleMenuIds = RoleMenuAccess::where('role_code', $roleCode)
            ->where('can_view', 1)
            ->pluck('menu_id');
    }
}

// Get all header menus
$menu = Menu::where('is_header', 'true')->orderBy('sort', 'ASC')->get();
                @endphp

                @foreach ($menu as $m)
                    @php
                        // Check if user has access to this header menu or any of its submenus
                        $menu_detail = Menu::where('parent', $m->id)->orderBy('sort', 'ASC')->get();

                        // Filter submenus based on access
                        $accessible_submenus = $menu_detail->filter(function ($item) use ($accessibleMenuIds) {
                            return $accessibleMenuIds->contains($item->id);
                        });

                        // Check if header menu itself is accessible OR has accessible submenus
                        $hasAccess = $accessibleMenuIds->contains($m->id) || $accessible_submenus->count() > 0;

                        // Skip this menu if user doesn't have access
                        if (!$hasAccess) {
                            continue;
                        }

                        // Check if current page is this menu or any of its submenus
                        $isActive = request()->is($m->url);
                        foreach ($accessible_submenus as $submenu) {
                            if (request()->is($submenu->url)) {
                                $isActive = true;
                                break;
                            }
                        }
                    @endphp
                    <li class="{{ $isActive ? 'active' : '' }}">
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
                        @if ($m->have_sub_menu == 'true' && $accessible_submenus->count() > 0)
                            <ul id="{{ $m->url }}" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                @foreach ($accessible_submenus as $item)
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
