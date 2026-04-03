<?php

// app/Http/Controllers/Api/AuthController.php

// Namespace = folder structure for organizing code
// This controller is inside App/Http/Controllers/Api folder
namespace App\Http\Controllers\Api;

// IMPORTS - bringing in classes we need to use
use App\Http\Controllers\Controller;  // Base controller class
use App\Models\User;                   // User model - represents the users table
use Illuminate\Http\Request;           // Handles incoming HTTP requests
use Illuminate\Support\Facades\Hash;   // For hashing/checking passwords
use Illuminate\Support\Facades\Validator;  // For validating input data

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REGISTER METHOD
    |--------------------------------------------------------------------------
    | This method handles new user registration
    | 
    | URL: POST http://localhost:8000/api/register
    | 
    | Expected Input (JSON):
    | {
    |     "name": "John Doe",
    |     "email": "john@example.com",
    |     "password": "password123",
    |     "password_confirmation": "password123"
    | }
    */
    public function register(Request $request)
    {
        // STEP 1: VALIDATE THE INPUT DATA
        // ─────────────────────────────────
        // Validator::make() checks if the incoming data meets our rules
        // $request->all() gets ALL data sent from the frontend
        $validator = Validator::make($request->all(), [
// dd($request->all()),
            // 'name' field rules:
            // required = must not be empty
            // string   = must be text (not number, not array)
            // max:255  = maximum 255 characters
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            // 'email' field rules:
            // required = must not be empty
            // string   = must be text
            // email    = must be valid email format (has @ and domain)
            // max:255  = maximum 255 characters
            // unique:users = must NOT already exist in 'users' table
            'email' => 'required|string|email|max:255|unique:users',

            // 'password' field rules:
            // required  = must not be empty
            // string    = must be text
            // min:8     = minimum 8 characters
            // confirmed = must have a matching 'password_confirmation' field
            'password' => 'required|string|min:8|confirmed',
        ]);

        // STEP 2: CHECK IF VALIDATION FAILED
        // ────────────────────────────────────
        // If any rule above is broken, return error messages
        if ($validator->fails()) {
            return response()->json([
                // 'status' helps frontend know if request succeeded or failed
                'status' => false,
                // 'message' gives a human-readable error description
                'message' => 'Validation Error',
                // 'errors' contains specific error for each field
                // Example: { "email": ["The email has already been taken."] }
                'errors' => $validator->errors()
            ], 422);
            // 422 = HTTP status code meaning "Unprocessable Entity"
            // (the server understood the request but the data is invalid)
        }

        // STEP 3: CREATE THE USER IN DATABASE
        // ─────────────────────────────────────
        // User::create() inserts a new row in the 'users' table
        $user = User::create([
            'name' => $request->name,          // Get 'name' from request
            'phone' => $request->phone,  
            'email' => $request->email,        // Get 'email' from request
            'password' => Hash::make($request->password),
            // Hash::make() converts plain text password to encrypted hash
            // Example: "password123" → "$2y$10$92IXUNpkjO0rOQ5byMi..."
            // This is for SECURITY - we never store plain passwords
        ]);

        // STEP 5: RETURN SUCCESS RESPONSE
        // ─────────────────────────────────
        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user' => $user,        // Send back user data
        ], 201);
        // 201 = HTTP status code meaning "Created"
        // (a new resource was successfully created)
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN METHOD
    |--------------------------------------------------------------------------
    | This method handles user login
    | 
    | URL: POST http://localhost:8000/api/login
    | 
    | Expected Input (JSON):
    | {
    |     "email": "john@example.com",
    |     "password": "password123"
    | }
    */
    public function login(Request $request)
    {
        // STEP 1: VALIDATE THE INPUT DATA
        // ─────────────────────────────────
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',     // Email is required and must be valid
            'password' => 'required|string',         // Password is required
        ]);

        // STEP 2: CHECK IF VALIDATION FAILED
        // ────────────────────────────────────
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // STEP 3: FIND THE USER BY EMAIL
        // ────────────────────────────────
        // where('email', ...) searches the users table for matching email
        // first() gets the first matching record (or null if not found)
        $user = User::where('email', $request->email)->first();

        // STEP 4: CHECK IF USER EXISTS AND PASSWORD IS CORRECT
        // ─────────────────────────────────────────────────────
        // !$user = user with this email doesn't exist
        // !Hash::check() = password doesn't match the stored hash
        // Hash::check('plain_password', 'hashed_password') returns true/false
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login credentials',
            ], 401);
            // 401 = HTTP status code meaning "Unauthorized"
            // (authentication failed)
        }

        // STEP 5: CREATE A NEW TOKEN FOR THIS LOGIN SESSION
        // ──────────────────────────────────────────────────
        $token = $user->createToken('auth_token')->plainTextToken;

        // STEP 6: RETURN SUCCESS RESPONSE WITH USER DATA AND TOKEN
        // ─────────────────────────────────────────────────────────
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            // Frontend will save this token and include it in future requests
        ], 200);
        // 200 = HTTP status code meaning "OK" (success)
    }

    /*
    |--------------------------------------------------------------------------
    | PROFILE METHOD
    |--------------------------------------------------------------------------
    | This method returns the currently logged-in user's data
    | This is a PROTECTED route - requires valid token
    | 
    | URL: GET http://localhost:8000/api/profile
    | Header: Authorization: Bearer <token>
    */
    public function profile(Request $request)
    {
        // $request->user() returns the authenticated user
        // This works because of Sanctum middleware checking the token
        return response()->json([
            'status' => true,
            'message' => 'Profile data',
            'user' => $request->user(),
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT METHOD
    |--------------------------------------------------------------------------
    | This method logs the user out by deleting their token
    | 
    | URL: POST http://localhost:8000/api/logout
    | Header: Authorization: Bearer <token>
    */
    public function logout(Request $request)
    {
        // Delete the token that was used for this request
        // This means the token can no longer be used
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}
