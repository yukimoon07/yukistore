<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\MissionGiftcode;
use App\Models\MissionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MissionController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Quản lý Nhiệm vụ Kiếm tiền';
        $missions = Mission::orderBy('id', 'desc')->paginate(15);
        $totalMissions = Mission::count();
        $totalCompleted = MissionHistory::count();
        $totalPaid = MissionHistory::sum('reward_amount');

        return view('admin.missions.index', compact('title', 'missions', 'totalMissions', 'totalCompleted', 'totalPaid'));
    }

    public function create()
    {
        $title = 'Thêm Nhiệm vụ mới';
        return view('admin.missions.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_url' => 'required|string',
            'reward_min' => 'required|numeric|min:1',
            'reward_max' => 'required|numeric|gte:reward_min',
            'cooldown_seconds' => 'required|integer|min:0',
        ]);

        $slug = Str::slug($request->title) . '-' . time();

        Mission::create([
            'title' => $request->title,
            'slug' => $slug,
            'image' => $request->image ?: '/assets/img/mission-default.png',
            'service_type' => $request->service_type ?: 'custom',
            'target_url' => $request->target_url,
            'api_token' => $request->api_token,
            'reward_min' => $request->reward_min,
            'reward_max' => $request->reward_max,
            'cooldown_seconds' => $request->cooldown_seconds,
            'description' => $request->description,
            'guide_steps' => $request->guide_steps,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('admin.missions.index')->with('success', 'Thêm nhiệm vụ thành công!');
    }

    public function edit($id)
    {
        $title = 'Chỉnh sửa Nhiệm vụ';
        $mission = Mission::findOrFail($id);
        return view('admin.missions.edit', compact('title', 'mission'));
    }

    public function update(Request $request, $id)
    {
        $mission = Mission::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'target_url' => 'required|string',
            'reward_min' => 'required|numeric|min:1',
            'reward_max' => 'required|numeric|gte:reward_min',
            'cooldown_seconds' => 'required|integer|min:0',
        ]);

        $mission->update([
            'title' => $request->title,
            'image' => $request->image ?: $mission->image,
            'service_type' => $request->service_type ?: $mission->service_type,
            'target_url' => $request->target_url,
            'api_token' => $request->api_token,
            'reward_min' => $request->reward_min,
            'reward_max' => $request->reward_max,
            'cooldown_seconds' => $request->cooldown_seconds,
            'description' => $request->description,
            'guide_steps' => $request->guide_steps,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('admin.missions.index')->with('success', 'Cập nhật nhiệm vụ thành công!');
    }

    public function destroy($id)
    {
        $mission = Mission::findOrFail($id);
        $mission->delete();
        return redirect()->route('admin.missions.index')->with('success', 'Xóa nhiệm vụ thành công!');
    }

    public function histories()
    {
        $title = 'Lịch sử Hoàn thành Nhiệm vụ';
        $histories = MissionHistory::with(['user', 'mission'])->orderBy('id', 'desc')->paginate(20);
        return view('admin.missions.histories', compact('title', 'histories'));
    }
}
