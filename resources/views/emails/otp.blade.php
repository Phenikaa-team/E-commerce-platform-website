<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mã OTP Xác Thực - ShopMart</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 24px;
            color: #1e293b;
        }
        .email-container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .email-banner {
            background: linear-gradient(135deg, #ef4444 0%, #f43f5e 100%);
            padding: 32px 24px;
            text-align: center;
        }
        .email-banner-title {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .email-banner-subtitle {
            color: #ffe4e6;
            margin: 6px 0 0;
            font-size: 13px;
        }
        .email-body {
            padding: 32px 24px;
        }
        .email-lead {
            font-size: 15px;
            margin-top: 0;
        }
        .email-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
        }
        .email-otp-wrapper {
            margin: 28px 0;
            text-align: center;
        }
        .email-otp-box {
            display: inline-block;
            background-color: #fff1f2;
            border: 2px dashed #f43f5e;
            border-radius: 12px;
            padding: 16px 36px;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #e11d48;
            user-select: all;
        }
        .email-expiry {
            font-size: 12px;
            color: #64748b;
            margin-top: 10px;
        }
        .email-warning-note {
            background-color: #f8fafc;
            border-left: 4px solid #ef4444;
            padding: 14px 16px;
            border-radius: 6px;
            margin: 24px 0;
        }
        .email-warning-text {
            margin: 0;
            font-size: 13px;
            color: #334155;
            line-height: 1.5;
        }
        .email-closing {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 0;
        }
        .email-footer-note {
            background-color: #f1f5f9;
            padding: 16px 24px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-banner">
            <h1 class="email-banner-title">ShopMart</h1>
            <p class="email-banner-subtitle">Hệ thống Thương mại Điện tử</p>
        </div>
        
        <div class="email-body">
            <p class="email-lead">Xin chào <strong>{{ $targetName }}</strong>,</p>
            <p class="email-text">
                Bạn đang thực hiện yêu cầu <strong>{{ $actionTitle }}</strong> trên tài khoản ShopMart. Dưới đây là mã xác thực OTP (One-Time Password) của bạn:
            </p>
            
            <div class="email-otp-wrapper">
                <div class="email-otp-box">
                    {{ $otp }}
                </div>
                <p class="email-expiry">
                    Mã xác thực có hiệu lực trong vòng <strong>5 phút</strong>.
                </p>
            </div>
            
            <div class="email-warning-note">
                <p class="email-warning-text">
                    <strong>Lưu ý quan trọng:</strong> Tuyệt đối không chia sẻ mã này cho bất kỳ ai, kể cả nhân viên hỗ trợ ShopMart. Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email hoặc liên hệ ngay bộ phận hỗ trợ.
                </p>
            </div>
            
            <p class="email-closing">Trân trọng,<br><strong>Đội ngũ bảo mật ShopMart</strong></p>
        </div>
        
        <div class="email-footer-note">
            Email tự động gửi từ hệ thống ShopMart. Vui lòng không phản hồi thư này. Hotline: 1900 8888.
        </div>
    </div>
</body>
</html>
