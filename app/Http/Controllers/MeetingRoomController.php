<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
use Illuminate\Http\Request;

class MeetingRoomController extends Controller
{
    public function index()
    {
        $meetingRooms = MeetingRoom::all();
        return view('meetingrooms.index', compact('meetingRooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'description' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        MeetingRoom::create($request->all());

        return redirect()->route('meetingrooms.index')->with('success', 'Meeting Room created successfully');
    }

    public function update(Request $request, MeetingRoom $meetingroom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'description' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        $meetingroom->update($request->all());

        return redirect()->route('meetingrooms.index')->with('success', 'Meeting Room updated successfully');
    }

    public function destroy(MeetingRoom $meetingroom)
    {
        $meetingroom->delete();
        return redirect()->route('meetingrooms.index')->with('success', 'Meeting Room deleted successfully');
    }
}
