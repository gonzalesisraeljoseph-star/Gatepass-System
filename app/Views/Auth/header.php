<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass Management System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: "Segoe UI", sans-serif;
            background: #f5f7fb;
        }

        .left-panel {
            background: linear-gradient(135deg, #0d6efd 0%, #003b8b 100%);
            color: white;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            top: -200px;
            right: -150px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -150px;
            left: -100px;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            max-width: 650px;
        }

        .system-badge {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 8px 20px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .feature-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 25px;
            height: 100%;
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .stats-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(20px);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
        }

        .login-side {
            background: #f8fafc;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,.08);
        }

        .logo-circle {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            background: linear-gradient(135deg,#0d6efd,#003b8b);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            box-shadow: 0 10px 30px rgba(13,110,253,.3);
        }

        .form-control {
            height: 55px;
            border-radius: 12px;
            border: 1px solid #dce3f1;
            padding-left: 15px;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
        }

        .btn-login {
            height: 55px;
            border-radius: 12px;
            font-weight: 600;
            background: linear-gradient(135deg,#0d6efd,#003b8b);
            border: none;
        }

        .btn-login:hover {
            opacity: .95;
        }

        .password-toggle {
            cursor: pointer;
        }

        @media(max-width:991px) {
            .left-panel {
                display: none !important;
            }

            .login-side {
                min-height: 100vh;
            }
        }
    </style>
</head>
