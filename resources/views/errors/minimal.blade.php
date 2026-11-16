<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $exception->getStatusCode() === 503 ? 'Bảo trì' : 'Có lỗi xảy ra' }} - ShopGame</title>
    <style>
        :root { color-scheme: dark; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0B0F16;
            color: #E5E7EB;
            padding: 24px;
        }
        .box {
            max-width: 520px;
            width: 100%;
            text-align: center;
            background: #111827;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 48px 32px;
        }
        .code {
            font-size: 72px;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #2563EB, #60A5FA);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            font-variant-numeric: tabular-nums;
        }
        h1 { font-size: 22px; font-weight: 700; margin-bottom: 10px; }
        p { color: #9CA3AF; font-size: 15px; line-height: 1.6; margin-bottom: 28px; }
        a.btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 10px;
            background: #2563EB;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: background .2s ease;
        }
        a.btn:hover { background: #1D4ED8; }
        .msg { color: #CBD5E1; font-size: 16px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="code">{{ $exception->getStatusCode() }}</div>
        @if($exception->getStatusCode() === 503)
            <h1>Website đang bảo trì</h1>
            <p class="msg">{{ config('app.maintenance_message', 'Chúng tôi đang nâng cấp hệ thống. Vui lòng quay lại sau ít phút — cảm ơn bạn đã kiên nhẫn!') }}</p>
        @elseif($exception->getStatusCode() === 404)
            <h1>Không tìm thấy trang</h1>
            <p>Trang bạn tìm không tồn tại hoặc đã bị di chuyển.</p>
        @elseif($exception->getStatusCode() === 403)
            <h1>Truy cập bị từ chối</h1>
            <p>Bạn không có quyền truy cập trang này.</p>
        @elseif($exception->getStatusCode() === 419)
            <h1>Phiên hết hạn</h1>
            <p>Trang đã hết hiệu lực, vui lòng thử lại.</p>
        @elseif($exception->getStatusCode() === 500)
            <h1>Có lỗi từ phía máy chủ</h1>
            <p>Chúng tôi đang khắc phục. Vui lòng thử lại sau.</p>
        @else
            <h1>Có lỗi xảy ra</h1>
            <p>Vui lòng thử lại sau.</p>
        @endif
        <a class="btn" href="{{ url('/') }}">Về trang chủ</a>
    </div>
</body>
</html>
