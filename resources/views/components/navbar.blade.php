<nav>
    <a href="{{ route('home') }}" class="brand">🚴 BikeProfile</a>
    <ul>
        <li>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                Home
            </a>
        </li>
        <li>
            <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                Profile
            </a>
        </li>
    </ul>
</nav>


