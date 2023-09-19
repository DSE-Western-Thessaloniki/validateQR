<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingsRequest;
use App\Models\Settings;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Settings::all()->first();
        if ($settings->img_filename === "") {
            return Inertia::render('Settings/Index', [
                'settings' => Settings::all(),
            ]);
        } else {
            $mime = mime_content_type(storage_path() . "/" . $settings->img_filename);
            return Inertia::render('Settings/Index', [
                'image' => 'data:' . $mime . ";base64," . base64_encode(file_get_contents(storage_path() . "/" . $settings->img_filename)),
                'settings' => Settings::all(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettingsRequest $request)
    {
        $settings = Settings::all();

        $imageFile = $request->file('image');

        if ($imageFile instanceof \Illuminate\Http\UploadedFile) {
            $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $imageFile->getClientOriginalName());
            // Remove any runs of periods
            $filename = mb_ereg_replace("([\.]{2,})", '', $filename);

            $imageFile->move(storage_path(), $filename);

            $data = $request->safe()->merge(['img_filename' => $filename])->toArray();
        } else {
            $data = $request->validated();
        }

        if ($settings->isEmpty()) {
            $settings = Settings::create($data);
        } else {
            $settings->first()->update($data);
        }

        return to_route('dashboard');
    }
}
