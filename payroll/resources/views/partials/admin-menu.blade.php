@foreach ($items as $item)
    @php
        $hasChildren = $item->children->isNotEmpty();
        $hasRoute = $item->routeName && \Illuminate\Support\Facades\Route::has($item->routeName);
        $url = $hasRoute
            ? route($item->routeName)
            : ($item->externalUrl ?? ($item->slug ? url($item->slug) : null));
    @endphp

    <li class="{{ $hasChildren ? 'submenu' : '' }}">
        <a href="{{ $url ?? '#' }}" target="{{ $item->target ?? '_self' }}">
            @if ($item->icon)
                <i class="{{ $item->icon }}"></i>
            @endif
            <span>{{ $item->title }}</span>
            @if ($hasChildren)
                <span class="menu-arrow"></span>
            @endif
        </a>

        @if ($hasChildren)
            <ul>
                @include('partials.admin-menu', ['items' => $item->children])
            </ul>
        @endif
    </li>
@endforeach

