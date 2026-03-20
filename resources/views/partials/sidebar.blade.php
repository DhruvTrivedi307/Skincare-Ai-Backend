<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="index.html" class="sidebar-logo">
            {{-- <img src="assets/images/logo.png" alt="site logo" class="light-logo">
            <img src="assets/images/logo-light.png" alt="site logo" class="dark-logo">
            <img src="assets/images/logo-icon.png" alt="site logo" class="logo-icon"> --}}
            {{-- <img src="https://www.pngkey.com/png/detail/175-1752225_random-logos-from-the-section-logos-of-musical.png" alt="" class="light-logo"> --}}
            <div>
                <span><b>Hello 👋! {{ auth()->user()->name ?? 'Guest' }}</b></span>
            </div>
        </a>
    </div>
    <div class="sidebar-menu-area">
        @if (Auth::user()->role === "Super-Admin")
            <ul class="sidebar-menu" id="sidebar-menu">
                {{-- <li class="dropdown active">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                        <span>Dashboard</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Home</a>
                        </li>
                        <li>
                            <a href="{{ route('users') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Buyers</a>
                        </li>
                        <li>
                            <a href="{{ route('activity-logs') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Activity Logs</a>
                        </li>
                        <li>
                            <a href="{{ route('usage-metadata') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> Usage Metadata</a>
                        </li>
                        <li>
                            <a href="{{ route('subscriptions') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i>Subscriptions</a>
                        </li>
                        <li>
                            <a href="{{ route('coupon-codes') }}"><i class="ri-circle-fill circle-icon text-info-main w-auto"></i>Coupons</a>
                        </li>
                    </ul>
                </li> --}}
                <li>
                    <a href="{{ route('dashboard') }}">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users') }}">
                        <iconify-icon icon="solar:user-id-linear" class="menu-icon"></iconify-icon>
                        <span>Buyers</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('activity-logs') }}">
                        <iconify-icon icon="solar:bill-list-linear" class="menu-icon"></iconify-icon>
                        <span>Activity Logs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('usage-metadata') }}">
                        <iconify-icon icon="solar:database-outline" class="menu-icon"></iconify-icon>
                        <span>Usage Metadata</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('subscriptions') }}">
                        <iconify-icon icon="solar:user-check-broken" class="menu-icon"></iconify-icon>
                        <span>Subscriptions</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('coupon-codes') }}">
                        <iconify-icon icon="solar:tag-price-outline" class="menu-icon"></iconify-icon>
                        <span>Coupons</span>
                    </a>
                </li>
            </ul>
        @else
            <ul class="sidebar-menu" id="sidebar-menu">
                <li>
                    <a href="{{ route('dashboard') }}">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users') }}">
                        <iconify-icon icon="solar:user-id-linear" class="menu-icon"></iconify-icon>
                        <span>Users</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('products') }}">
                        <iconify-icon icon="solar:widget-5-linear" class="menu-icon"></iconify-icon>
                        <span>Products</span>
                    </a>
                </li>
            </ul>
        @endif
    </div>
</aside>