<?php

return [
    'validate' => [
        'fail' => 'Lỗi xác thực'
    ],
    'error' => [
        'create' => ':item không thể tạo.',
        'update' => ':item không thể cập nhật.',
        'delete' => ':item không thể xóa.',
        'list' => ':item không thể liệt kệ.',
        'show' => ':item không thể hiển thị.',
        'permission' => 'Bạn không có quyền.'
    ],
    'success' => [
        'create' => 'Tạo :item thành công.',
        'update' => 'Cập nhật :item thành công.',
        'delete' => 'Xóa :item thành công.',
        'list' => 'Liệt kê :item thành công.',
        'show' => 'Hiển thị :item thành công.',
    ],
    App\Models\User::class => [
        'not_found' => 'Nhân viên không tồn tại.'
    ],
];
