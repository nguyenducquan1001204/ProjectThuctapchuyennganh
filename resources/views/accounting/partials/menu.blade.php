@php
    use Illuminate\Support\Facades\Route;
@endphp

@if(isset($accountingMenuItems) && $accountingMenuItems->count() > 0)
    @foreach($accountingMenuItems as $menuItem)
        @php
            $hasChildren = $menuItem->children && $menuItem->children->count() > 0;
            $isActive = false;
            $url = '#';
            
            // Xác định URL - Chỉ dùng routeName
            if ($menuItem->externalUrl) {
                $url = $menuItem->externalUrl;
            } elseif ($menuItem->routeName) {
                // Lấy routeName từ database và trim để loại bỏ khoảng trắng
                $routeName = trim($menuItem->routeName);
                
                // Xử lý các trường hợp đặc biệt (tương thích ngược)
                if ($routeName === 'dashboard') {
                    $routeName = 'accounting.dashboard';
                }
                
                // Tạo URL từ route name
                if (Route::has($routeName)) {
                    $url = route($routeName);
                    
                    // Xác định trạng thái active
                    $currentRoute = Route::currentRouteName();
                    $isActive = $currentRoute === $routeName || 
                                ($menuItem->routeName === 'dashboard' && $currentRoute === 'accounting.dashboard');
                } else {
                    // Route không tồn tại
                    $url = '#';
                }
            } else {
                // Không có routeName, dùng # làm fallback
                $url = '#';
            }
        @endphp

        @if($hasChildren)
            <li class="submenu {{ $isActive ? 'active' : '' }}">
                <a href="#">
                    @if($menuItem->icon)
                        <i class="{{ $menuItem->icon }}"></i>
                    @else
                        <i class="feather-grid"></i>
                    @endif
                    <span>{{ $menuItem->title }}</span>
                    <span class="menu-arrow"></span>
                </a>
                <ul>
                    @foreach($menuItem->children as $child)
                        @php
                            $childUrl = '#';
                            $childActive = false;
                            
                            if ($child->externalUrl) {
                                $childUrl = $child->externalUrl;
                            } elseif ($child->routeName) {
                                // Lấy routeName từ database và trim để loại bỏ khoảng trắng
                                $childRouteName = trim($child->routeName);
                                
                                // Xử lý các trường hợp đặc biệt (tương thích ngược)
                                if ($childRouteName === 'dashboard') {
                                    $childRouteName = 'accounting.dashboard';
                                }
                                
                                // Tạo URL từ route name
                                if (Route::has($childRouteName)) {
                                    $childUrl = route($childRouteName);
                                    
                                    // Xác định trạng thái active
                                    $currentRoute = Route::currentRouteName();
                                    $childActive = $currentRoute === $childRouteName || 
                                                  ($child->routeName === 'dashboard' && $currentRoute === 'accounting.dashboard');
                                } else {
                                    // Route không tồn tại
                                    $childUrl = '#';
                                }
                            } else {
                                // Không có routeName, dùng # làm fallback
                                $childUrl = '#';
                            }
                        @endphp
                        <li>
                            <a href="{{ $childUrl }}" 
                               @if($child->target) target="{{ $child->target }}" @endif
                               class="{{ $childActive ? 'active' : '' }}">
                                {{ $child->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @else
            <li class="{{ $isActive ? 'active' : '' }}">
                <a href="{{ $url }}" 
                   @if($menuItem->target) target="{{ $menuItem->target }}" @endif>
                    @if($menuItem->icon)
                        <i class="{{ $menuItem->icon }}"></i>
                    @else
                        <i class="feather-grid"></i>
                    @endif
                    <span>{{ $menuItem->title }}</span>
                </a>
            </li>
        @endif
    @endforeach
@else
    {{-- Menu mặc định nếu chưa có menu items trong database --}}
    <li class="{{ request()->routeIs('accounting.dashboard') ? 'active' : '' }}">
        <a href="{{ route('accounting.dashboard') }}">
            <i class="fas fa-home"></i> <span>Bảng điều khiển kế toán</span>
        </a>
    </li>
@endif
