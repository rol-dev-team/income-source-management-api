<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
// GET /users
public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $users = User::paginate($pageSize);

        return response()->json([
        'data' => $users->items(),
        'total' => $users->total(),
        ]);
    }

// POST /users
public function store(Request $request)
{
$validated = $request->validate([
'name'  => 'required|string|max:255',
'email' => 'required|email|unique:users,email',
]);

$user = User::create($validated);

return response()->json($user, 201);
}

// PUT /users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->all();

        // If password is present, hash it
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // If role is not present in the request, remove it so it won’t overwrite
        if (!array_key_exists('role', $data)) {
            unset($data['role']);
        }

        $user->update($data);

        return response()->json($user);
    }


// DELETE /users/{id}
public function destroy($id)
{
$user = User::findOrFail($id);
$user->delete();

return response()->json(['message' => 'User deleted']);
}
}
