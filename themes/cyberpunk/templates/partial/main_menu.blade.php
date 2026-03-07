@php
    $groups = [];
    foreach ($main_menu as $item) {
        $parent = $item['parent'] ?? null;
        if ($parent) $groups[$parent][] = $item;
    }

    $finalMenu = [];
    $processedRoutes = [];
    
    foreach ($main_menu as $item) {
        if (($item['parent'] ?? null) !== null) continue;
        $route = $item['route'] ?? $item['label'] ?? null;
        if (in_array($route, $processedRoutes)) continue;
        
        $finalMenu[] = [
            'item' => $item,
            'children' => $groups[$route] ?? [],
            'isGroup' => isset($groups[$route]) && !empty($groups[$route])
        ];
        $processedRoutes[] = $route;
    }

    $groupMetadata = [
        'admin_group' => ['label' => 'Administración', 'icon' => 'fas fa-cogs', 'order' => 90],
        'WorkFrame.Mail' => ['label' => 'Correo electrónico', 'icon' => 'fas fa-envelope', 'order' => 60],
        'WorkFrame.Report' => ['label' => 'Informes', 'icon' => 'fas fa-chart-pie', 'order' => 70],
    ];

    foreach ($groups as $parentKey => $children) {
        if (!in_array($parentKey, $processedRoutes)) {
            $label = $parentKey;
            $icon = 'fas fa-terminal';
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
    usort($finalMenu, fn($a, $b) => ($a['item']['order'] ?? 100) <=> ($b['item']['order'] ?? 100));
@endphp

<div class="sidebar cyber-sidebar">
    <div class="cyber-scanline"></div>
    <div class="cyber-logo text-center py-4">
        <h2 class="glitch" data-text="ALXARAFE">ALXARAFE</h2>
        <small class="text-secondary">> SYS.MOD_WORKFRAME</small>
    </div>

    <div class="cyber-section mb-4">
        <h6 class="cyber-header px-3 mt-4 text-secondary">
            <i class="fas fa-terminal me-2"></i>NAVIGATION_INDEX
        </h6>
        <div class="d-flex flex-column">
            @foreach($finalMenu as $entry)
                @php 
                    $item = $entry['item']; 
                    $isGroup = $entry['isGroup'];
                    $uid = md5((string)($item['route'] ?? $item['label']));
                    $targetId = 'cyber-menu-' . $uid;
                @endphp
                
                @if($isGroup)
                    <div class="cyber-group-wrapper mb-1">
                        <a href="#{{ $targetId }}" class="cyber-link d-block px-3 py-2 text-decoration-none" data-bs-toggle="collapse" aria-expanded="false">
                            <span class="cyber-marker">[+]</span> 
                            @if(!empty($item['icon']))<i class="{{ $item['icon'] }} me-2"></i>@endif
                            {{ strtoupper($item['label']) }}
                        </a>
                        <div class="collapse ps-3" id="{{ $targetId }}">
                            @foreach($entry['children'] as $child)
                                <a href="{{ $child['url'] }}" class="cyber-link d-block px-3 py-1 small opacity-75 text-decoration-none">
                                    <span class="cyber-marker">|_</span> 
                                    @if(!empty($child['icon']))<i class="{{ $child['icon'] }} me-1"></i>@endif
                                    {{ strtoupper($child['label']) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}" class="cyber-link d-block px-3 py-2 text-decoration-none">
                        <span class="cyber-marker">[ ]</span> 
                        @if(!empty($item['icon']))<i class="{{ $item['icon'] }} me-2"></i>@endif
                        {{ strtoupper($item['label']) }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <div class="cyber-footer mt-auto p-3 text-center border-top border-secondary border-opacity-25 bg-black">
        <small class="text-muted d-block code-font">IP: 127.0.0.1_</small>
        <small class="text-muted code-font">AUTH: OK_</small>
    </div>
</div>

<style>
    .cyber-sidebar {
        height: 100vh;
        background: #000;
        color: #0ff;
        font-family: var(--code-font, 'Courier New', Courier, monospace);
        position: relative;
        overflow: hidden;
        border-right: 1px solid #0ff3;
        display: flex;
        flex-direction: column;
    }
    .cyber-link { color: #0ff; opacity: 0.8; transition: all 0.2s; }
    .cyber-link:hover { opacity: 1; background: rgba(0, 255, 255, 0.1); color: #fff !important; text-shadow: 0 0 5px #0ff; }
    .cyber-marker { color: #f0f; margin-right: 5px; font-weight: bold; }
    .cyber-header { letter-spacing: 2px; font-size: 0.75rem; border-bottom: 1px solid rgba(0, 255, 255, 0.2); margin-bottom: 10px; padding-bottom: 5px; }
</style>
