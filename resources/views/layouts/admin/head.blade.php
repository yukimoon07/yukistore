<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin Dashboard')</title>

    <link rel="icon" href="{{ config_get('site_favicon') ?: asset('img/favicon.svg') }}" type="image/svg+xml">
    
    <!-- Plugins from old theme -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}">
    
    <!-- New theme CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.css" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/inter/inter.css" id="main-font-link" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/phosphor/duotone/style.css" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/tabler-icons.min.css" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/feather.css" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/fontawesome.css" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/fonts/material.css" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/css/style.css" id="main-style-link" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/css/style-preset.css" />
    <link rel="stylesheet" href="/cmsbvq/template/frontend/css/custom.css" />

    <style>
        .image-upload {
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .image-upload:hover, .image-upload.is-dragover {
            border-color: #2563eb !important;
            background: rgba(37, 99, 235, 0.08) !important;
        }
        .image-upload input[type="file"] {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            opacity: 0 !important;
            cursor: pointer !important;
            z-index: 10 !important;
        }
        .image-upload .image-uploads {
            pointer-events: none !important;
            user-select: none !important;
        }
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }
        .image-preview-item {
            position: relative;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            background: rgba(0,0,0,0.25);
            padding: 8px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .image-preview-item img {
            max-height: 110px;
            max-width: 160px;
            object-fit: contain;
            border-radius: 6px;
        }
        .image-preview-meta {
            font-size: 11px;
            color: #10b981;
            font-weight: 600;
            margin-top: 6px;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
    


    @stack('css')
    @yield('head')
</head>
