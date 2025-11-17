@extends('layouts.client')

@section('title', 'Đặt hàng thành công - QLXM')

@section('content')
    <div class="page-heading products-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>Đặt hàng thành công</h4>
                        <h2>cảm ơn bạn đã tin tương</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="products">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="section-heading text-center mb-5">
                        <h2>Đơn hàng của bạn đã được xác nhận!</h2>
                    </div>

                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>Đặt hàng thành công!</h4>
                        <p class="mb-0">
                            {{ session('success') ?? 'Đơn hàng của bạn đã được ghi nhận. Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.' }}
                        </p>
                    </div>





                    <div class="text-center mt-4">
                        <h6>Bước tiếp theo:</h6>
                        <p class="text-muted mb-4">
                            <i class="fas fa-phone mr-2"></i>Nhân viên của chúng tôi sẽ gọi điện xác nhận đơn hàng trong
                            vòng 24h.<br>
                            <i class="fas fa-truck mr-2"></i>Đơn hàng sẽ được giao trong vòng 2-3 ngày làm việc.<br>
                            <i class="fas fa-envelope mr-2"></i>Thông tin chi tiết đã được gửi đến email của bạn.
                        </p>

                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('client.home') }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-home mr-2"></i>Về trang chủ
                                </a>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <a href="{{ route('client.motorcycles') }}" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-shopping-cart mr-2"></i>Tiếp tục mua sắm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .page-heading {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }

        .alert-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            border: none;
            border-radius: 15px;
            color: white;
            font-weight: 500;
            box-shadow: 0 10px 30px rgba(86, 171, 47, 0.3);
            margin: 30px 0;
        }

        .alert-success i {
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .alert-success h4 {
            color: white;
            font-weight: bold;
            margin: 15px 0 10px 0;
        }

        .section-heading h2 {
            color: #2c3e50;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .text-center h6 {
            color: #34495e;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .text-muted {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #007bff;
            line-height: 1.8;
            font-size: 1rem;
        }

        .text-muted i {
            color: #007bff;
            width: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .container {
            max-width: 900px;
        }

        .col-md-8 {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-top: -50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }

        .products {
            padding: 0 0 80px 0;
        }

        /* Debug box styling */
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            color: #856404;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        /* Animation for success icon */
        @keyframes bounce {

            0%,
            20%,
            60%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-20px);
            }

            80% {
                transform: translateY(-10px);
            }
        }

        .fa-check-circle {
            animation: bounce 2s infinite;
        }
    </style>
@endsection
