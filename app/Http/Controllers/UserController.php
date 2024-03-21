<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('User/Index', ['users' => User::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('User/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role']
        ]);

        return to_route('user.index')
            ->with('success', 'Ο νέος χρήστης δημιουργήθηκε!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('User/Edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        if (is_null($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return to_route('user.index')
            ->with('success', 'Τα στοιχεία του χρήστη ενημερώθηκαν!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $result = $user->delete();

        if ($result) {
            return to_route('user.index')
                ->with('success', 'O χρήστης διαγράφηκε!');
        } else {
            return to_route('user.index')
                ->with('danger', 'Η διαγραφή του χρήστη απέτυχε!');
        }
    }

    public function toggleActive(User $user)
    {
        $user->active = !$user->active;

        if ($user->save()) {
            return json_encode(['success' => true, 'message' => 'Η αλλαγή αποθηκεύτηκε επιτυχώς']);
        }

        return json_encode(['success' => false, 'message' => 'Σφάλμα τροποποίησης λογαριασμού']);
    }
}
