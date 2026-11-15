<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index()
    {
        $data['title']   = 'SuperAdmin - My Profile';
        $data['userInfo'] = User::with('company')->findOrFail(Auth::id());
        return view('superadmin.profile', $data);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'password'         => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ], [
            'password.required'         => 'New password is required.',
            'confirm_password.required' => 'Please confirm your new password.',
            'confirm_password.same'     => 'The confirmation password does not match.',
        ]);

        try {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Superadmin password updated successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $user   = User::findOrFail($userId);

        // Format phone number before validation
        if ($request->filled('phone')) {
            $cleanPhone = preg_replace('/\D/', '', $request->phone);
            if (strlen($cleanPhone) >= 10) {
                $formattedPhone = substr($cleanPhone, 0, 3) . '-' . substr($cleanPhone, 3, 3) . '-' . substr($cleanPhone, 6, 4);
                $request->merge(['phone' => $formattedPhone]);
            }
        }

        $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'phone'         => 'required|string|max:20|unique:users,phone,' . $userId,
            'email'         => 'required|email|max:255|unique:users,email,' . $userId,
            'employee_code' => 'nullable|string|max:30',
            'designation'   => 'nullable|string|max:100',
            'department'    => 'nullable|string|max:100',
            'dob'           => 'nullable|date',
            'gender'        => 'nullable|in:1,2,3',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'email.required' => 'Email address is required.',
            'email.unique'   => 'This email address is already registered by another account.',
            'phone.required' => 'Contact number is required.',
            'phone.unique'   => 'This contact number is already registered by another account.',
        ]);

        try {
            $imagePath = $user->profile_image;

            if ($request->hasFile('profile_image')) {
                if ($imagePath && File::exists(public_path($imagePath))) {
                    File::delete(public_path($imagePath));
                }

                $image = $request->file('profile_image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $destPath = public_path('uploads/profile_images');

                if (!File::exists($destPath)) {
                    File::makeDirectory($destPath, 0755, true);
                }

                $image->move($destPath, $imageName);
                $imagePath = 'uploads/profile_images/' . $imageName;
            }

            $user->update([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'phone'         => $request->phone,
                'email'         => $request->email,
                'employee_code' => $request->employee_code ? strtoupper($request->employee_code) : null,
                'designation'   => $request->designation,
                'department'    => $request->department,
                'dob'           => $request->dob,
                'gender'        => $request->gender,
                'profile_image' => $imagePath,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Profile updated successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Profile Update Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update profile.',
            ], 500);
        }
    }

    public function removeProfileImage(): JsonResponse
    {
        $user = User::findOrFail(Auth::id());

        if (!empty($user->profile_image)) {
            $imagePath = public_path($user->profile_image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $user->profile_image = null;
            $user->save();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile image removed successfully.',
        ]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $user = User::findOrFail(Auth::id());
            $imagePath = $user->profile_image;

            if ($imagePath && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }

            $image = $request->file('profile_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destPath = public_path('uploads/profile_images');

            if (!File::exists($destPath)) {
                File::makeDirectory($destPath, 0755, true);
            }

            $image->move($destPath, $imageName);
            $user->profile_image = 'uploads/profile_images/' . $imageName;
            $user->save();

            return response()->json([
                'status'    => 'success',
                'message'   => 'Profile photo updated successfully.',
                'image_url' => asset($user->profile_image),
            ]);
        } catch (\Throwable $e) {
            Log::error('Profile Image Upload Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to upload profile image.',
            ], 500);
        }
    }
}
