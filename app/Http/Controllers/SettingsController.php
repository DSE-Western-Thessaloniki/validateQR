<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingsRequest;
use App\Models\Settings;
use Exception;
use Inertia\Inertia;

class SettingsController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Settings::class, 'settings');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Settings::all();

        // Αν η βάση δεν έχει ρυθμίσεις φτιάξε μια εγγραφή με τις
        // προκαθορισμένες ρυθμίσεις
        if ($settings->isEmpty()) {
            Settings::create();
            $settings = Settings::all()->first();
        } else {
            $settings = $settings->first();
        }

        return Inertia::render('Settings/Index', [
            'settings' => Settings::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettingsRequest $request)
    {
        $settings = Settings::all();

        $data = $request->validated();

        if ($settings->isEmpty()) {
            $settings = Settings::create($data);
        } else {
            if (!$settings->first()->update($data)) {
                throw new Exception("Error saving settings!");
            }
        }

        return to_route('dashboard');
    }
}
