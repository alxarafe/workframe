@php
    $groups = [];
    foreach ($main_menu as $item) {
        $parent = $item['parent'] ?? null;
        if ($parent) $groups[$parent][] = $item;
    }

    $finalMenu = [];
    $processedRoutes = [];
    
    // 1. Process items from main_menu
    foreach ($main_menu as $item) {
        $parent = $item['parent'] ?? null;
        if ($parent) continue; // Skip children, they will be picked via groups

        $route = $item['route'] ?? $item['label'] ?? null;
        if (in_array($route, $processedRoutes)) continue;
        
        $finalMenu[] = [
            'item' => $item,
            'children' => $groups[$route] ?? [],
            'isGroup' => isset($groups[$route]) && !empty($groups[$route])
        ];
        $processedRoutes[] = $route;
    }

    // 2. Add virtual groups for parents not found as roots
    $groupMetadata = [
        'admin_group' => ['label' => 'Administración', 'icon' => 'fas fa-cogs', 'order' => 90],
        'WorkFrame.Mail' => ['label' => 'Correo electrónico', 'icon' => 'fas fa-envelope', 'order' => 60],
        'WorkFrame.Report' => ['label' => 'Informes', 'icon' => 'fas fa-chart-pie', 'order' => 70],
    ];

    foreach ($groups as $parentKey => $children) {
        if (!in_array($parentKey, $processedRoutes)) {
            $label = $parentKey;
            $icon = 'fas fa-folder';
            $order = 100;
            if (isset($groupMetadata[$parentKey])) {
                $label = $groupMetadata[$parentKey]['label'];
                $icon = $groupMetadata[$parentKey]['icon'];
                $order = $groupMetadata[$parentKey]['order'];
            }

            $finalMenu[] = [
                'item' => ['label' => $label, 'icon' => $icon, 'url' => '#', 'order' => $order, 'route' => $parentKey],
                'children' => $children,
                'isGroup' => true
            ];
            $processedRoutes[] = $parentKey;
        }
    }

    // Sort the final menu
    usort($finalMenu, fn($a, $b) => ($a['item']['order'] ?? 100) <=> ($b['item']['order'] ?? 100));
@endphp

<div class="sidebar shadow-sm" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 border-bottom">
        <a href="index.php" class="text-decoration-none text-dark d-flex align-items-center justify-content-center">
            <i class="{{ \Alxarafe\Base\Config::getConfig()->main->appIcon ?? 'fas fa-rocket' }} text-primary me-2"></i>
            <span class="fw-bold text-uppercase fs-5">{{ \Alxarafe\Base\Config::getConfig()->main->appName ?? 'Alxarafe' }}</span>
        </a>
    </div>

    <div class="list-group list-group-flush mt-3 px-2">
        @foreach($finalMenu as $entry)
            @php 
                $item = $entry['item'];
                $isGroup = $entry['isGroup'];
                $uid = md5((string)($item['route'] ?? $item['label']));
                $targetId = 'menu-' . $uid;
                $isActive = str_contains($_SERVER['REQUEST_URI'] ?? '', ($item['url'] ?? '---') === '#' ? '---' : ($item['url'] ?? '---'));
            @endphp

            @if($isGroup)
                <div class="menu-item-group mb-1">
                    <a href="#{{ $targetId }}" 
                       class="list-group-item list-group-item-action d-flex align-items-center justify-content-between rounded-3 border-0 py-2 {{ $isActive ? 'active' : '' }}"
                       data-bs-toggle="collapse" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            @if(!empty($item['icon']))
                                <i class="{{ $item['icon'] }} me-3 width-20 text-center opacity-75"></i>
                            @endif
                            <span class="small fw-semibold">{{ $item['label'] }}</span>
                        </div>
                        <i class="fas fa-chevron-right small ms-auto transform-icon" style="font-size: 0.7rem;"></i>
                    </a>
                    <div class="collapse ps-4 mt-1 shadow-inner rounded-3 bg-light bg-opacity-10" id="{{ $targetId }}">
                        @foreach($entry['children'] as $child)
                            <a href="{{ $child['url'] }}" 
                               class="list-group-item list-group-item-action border-0 py-1 ps-4 small rounded-3 opacity-75 hover-opacity-100">
                                <span>{{ $child['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $item['url'] }}" 
                   class="list-group-item list-group-item-action d-flex align-items-center mb-1 rounded-3 border-0 py-2 {{ $isActive ? 'active' : '' }}">
                    @if(!empty($item['icon']))
                        <i class="{{ $item['icon'] }} me-3 width-20 text-center opacity-75"></i>
                    @endif
                    <span class="small fw-semibold">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </div>

    <div class="sidebar-footer mt-auto p-3 border-top bg-light bg-opacity-30 d-flex justify-content-between">
        <span class="small text-muted">&copy; {{ date('Y') }} WorkFrame</span>
        <a href="index.php?module=Admin&controller=Auth&action=logout" class="text-danger" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</div>

<style>
    /* Fixed sidebar constraints */
    #id_container.has-sidebar #sidebar-wrapper {
        width: 260px;
        background: #fff;
        display: flex;
        flex-direction: column;
        border-right: 1px solid rgba(0,0,0,0.05);
        height: 100vh;
        z-index: 1000;
        position: sticky;
        top: 0;
    }
    .width-20 { width: 20px; }
    .list-group-item-action.active {
        background-color: var(--bs-primary-bg-subtle, #e7f1ff) !important;
        color: var(--bs-primary, #0d6efd) !important;
        font-weight: 600;
    }
    [aria-expanded="true"] .transform-icon { transform: rotate(90deg); }
    .transform-icon { transition: transform 0.2s ease; }
    .hover-opacity-100:hover { opacity: 1 !important; }
</style>
