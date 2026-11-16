<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\GameService;
use Illuminate\Http\Request;

class GameServiceController extends Controller
{
    //
    public function show($slug)
    {
        $service = GameService::with(['activePackages'])->where('slug', $slug)->firstOrFail();
        $title = $service->name;
        return view('user.service.show', compact('service', 'title'));
    }
    public function showAll()
    {
        $title = 'Dịch vụ thuê';
        // Chỉ hiển thị dịch vụ đang bật; gói hiển thị bên trong là gói hợp lệ (đang bật + có giá).
        $services = GameService::where('active', true)
            ->with('activePackages')
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('user.service.show-all', compact('services', 'title'));
    }
}
