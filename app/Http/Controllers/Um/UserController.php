<?php

namespace App\Http\Controllers\Um;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Module;
use App\Models\AssignModule;
use App\Models\UserModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
   public function index(Request $request)
   {
      $data['title'] = 'UM - Users Management';
      $data['role'] = 3;

      return view('um.view_user', $data);
   }

   public function admin(Request $request)
   {
      $data['title'] = 'UM - Admins Management';
      $data['role'] = 2;

      return view('um.view_user', $data);
   }

   public function datatable(Request $request)
   {
      $role = $request->get('role', 3);
      $companyId = auth()->user()->company_id;

      $users = User::with('company')
         ->where('company_id', $companyId)
         ->where('role', $role);

      return DataTables::of($users)
         ->addColumn('user_details', function ($row) {
            $name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?: ($row->name ?? 'N/A');
            return '<div class="d-flex align-items-center gap-3">
               <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width:38px; height:38px; font-size:15px; flex-shrink:0;">
                  ' . strtoupper(substr($name, 0, 1)) . '
               </div>
               <div>
                  <div class="fw-bold text-dark">' . e($name) . '</div>
                  <small class="text-muted">' . e($row->email) . '</small>
               </div>
            </div>';
         })
         ->addColumn('company_name', function ($row) {
            return $row->company ? '<span class="fw-semibold text-dark"><i class="bi bi-building me-1 text-primary"></i>' . e($row->company->company_name) . '</span>' : '<span class="text-muted">N/A</span>';
         })
         ->addColumn('contact_info', function ($row) {
            return '<span class="small text-muted">' . e($row->phone ?? 'No Contact') . '</span>';
         })
         ->addColumn('status_badge', function ($row) {
            $checked = (int) $row->status === 1 ? 'checked' : '';
            return '<div class="form-check form-switch d-flex justify-content-center align-items-center mb-0">
               <input class="form-check-input status-toggle" type="checkbox" data-id="' . $row->id . '" ' . $checked . ' style="cursor:pointer; width:38px; height:20px;">
            </div>';
         })
         ->addColumn('action', function ($row) {
            $html = '<div class="d-flex align-items-center justify-content-center gap-2">';
            // 1. Edit Button
            $html .= '<a href="' . route('um.users.edit', $row->id) . '" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit Account" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-pencil"></i></a>';
            // 2. Change Password Button
            $html .= '<button type="button" class="btn btn-sm btn-outline-success rounded-circle changePasswordBtn" data-userid="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#changePasswordModal" title="Reset Password" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-key"></i></button>';
            // 3. Manage Modules Popup Button
            $html .= '<button type="button" class="btn btn-sm btn-outline-secondary rounded-circle manageModulesBtn" data-userid="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#manageModulesModal" title="Manage Modules" style="width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-gear"></i></button>';
            $html .= '</div>';
            return $html;
         })
         ->rawColumns(['user_details', 'company_name', 'contact_info', 'status_badge', 'action'])
         ->make(true);
   }

   public function create(Request $request)
   {
      $data['title'] = 'UM - Add New User / Admin';
      $data['role'] = $request->get('role', 3);

      $companyId = auth()->user()->company_id;

      $assignedModuleIds = AssignModule::where('company_id', $companyId)->pluck('module_id');
      $data['groupedModules'] = Module::whereIn('id', $assignedModuleIds)
         ->where('status', 1)
         ->orderBy('group_order')
         ->get()
         ->groupBy('group_name');

      return view('um.add_user', $data);
   }

   public function store(Request $request): JsonResponse
   {
      if ($request->filled('phone')) {
         $cleanPhone = preg_replace('/\D/', '', $request->phone);
         if (strlen($cleanPhone) >= 10) {
            $formattedPhone = substr($cleanPhone, 0, 3) . '-' . substr($cleanPhone, 3, 3) . '-' . substr($cleanPhone, 6, 4);
            $request->merge(['phone' => $formattedPhone]);
         }
      }

      $request->validate([
         'first_name' => 'required|string|max:255',
         'email'      => 'required|email|unique:users,email',
         'phone'      => 'required|string|max:20|unique:users,phone',
         'password'   => 'required|min:6',
         'role'       => 'required|in:2,3',
         'modules'    => 'required|array|min:1',
         'modules.*'  => 'exists:modules,id',
      ]);

      $companyId = auth()->user()->company_id;

      $user = User::create([
         'first_name' => $request->first_name,
         'last_name'  => $request->last_name,
         'email'      => $request->email,
         'password'   => Hash::make($request->password),
         'company_id' => $companyId,
         'role'       => $request->role,
         'phone'      => $request->phone,
         'gender'     => $request->gender,
         'status'     => 1,
      ]);

      if ($request->has('modules')) {
         foreach ($request->modules as $moduleId) {
            UserModule::create([
               'company_id' => $companyId,
               'user_id'    => $user->id,
               'module_id'  => $moduleId,
            ]);
         }
      }

      return response()->json([
         'status'  => 'success',
         'message' => 'Account and modules assigned successfully.',
      ]);
   }

   public function edit($id)
   {
      $companyId = auth()->user()->company_id;
      $data['title'] = 'UM - Edit User / Admin';

      $data['user'] = User::where('company_id', $companyId)->findOrFail($id);

      $assignedModuleIds = AssignModule::where('company_id', $companyId)->pluck('module_id');

      $data['groupedModules'] = Module::whereIn('id', $assignedModuleIds)
         ->where('status', 1)
         ->orderBy('group_order')
         ->get()
         ->groupBy('group_name');

      $data['userModules'] = UserModule::where('user_id', $id)->pluck('module_id')->toArray();

      return view('um.edit_user', $data);
   }

   public function update(Request $request, $id): JsonResponse
   {
      $companyId = auth()->user()->company_id;
      $user = User::where('company_id', $companyId)->findOrFail($id);

      if ($request->filled('phone')) {
         $cleanPhone = preg_replace('/\D/', '', $request->phone);
         if (strlen($cleanPhone) >= 10) {
            $formattedPhone = substr($cleanPhone, 0, 3) . '-' . substr($cleanPhone, 3, 3) . '-' . substr($cleanPhone, 6, 4);
            $request->merge(['phone' => $formattedPhone]);
         }
      }

      $request->validate([
         'first_name' => 'required|string|max:255',
         'email'      => 'required|email|unique:users,email,' . $id,
         'phone'      => 'required|string|max:20|unique:users,phone,' . $id,
         'role'       => 'required|in:2,3',
         'modules'    => 'required|array|min:1',
         'modules.*'  => 'exists:modules,id',
      ]);

      try {
         $user->first_name = $request->first_name;
         $user->last_name = $request->last_name;
         $user->email = $request->email;
         if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
         }
         $user->role = $request->role;
         $user->phone = $request->phone;
         $user->gender = $request->gender;
         $user->save();

         UserModule::where('user_id', $user->id)->delete();
         foreach ($request->modules as $moduleId) {
            UserModule::create([
               'company_id' => $companyId,
               'user_id'    => $user->id,
               'module_id'  => $moduleId,
            ]);
         }

         return response()->json([
            'status'  => 'success',
            'message' => 'User and module permissions updated successfully.',
         ]);
      } catch (\Throwable $e) {
         return response()->json([
            'status'  => 'error',
            'message' => 'Failed to update user: ' . $e->getMessage()
         ], 500);
      }
   }

   public function updateStatus(Request $request): JsonResponse
   {
      $request->validate([
         'id'     => 'required|integer|exists:users,id',
         'status' => 'required|in:0,1',
      ]);

      $companyId = auth()->user()->company_id;
      $user = User::where('company_id', $companyId)->findOrFail($request->id);
      $user->status = (int) $request->status;
      $user->save();

      return response()->json([
         'status'  => 'success',
         'message' => 'Status updated successfully.',
      ]);
   }

   /**
    * Reset Password AJAX
    */
   public function updatePassword(Request $request): JsonResponse
   {
      $request->validate([
         'user_id'  => 'required|integer|exists:users,id',
         'password' => 'required|string|min:6',
      ]);

      $companyId = auth()->user()->company_id;
      $user = User::where('company_id', $companyId)->findOrFail($request->user_id);

      $user->password = Hash::make($request->password);
      $user->save();

      return response()->json([
         'status'  => 'success',
         'message' => 'Password updated successfully.',
      ]);
   }

   /**
    * Fetch User Modules for Popup
    */
   public function getUserModules(Request $request): JsonResponse
   {
      $companyId = auth()->user()->company_id;
      $userId = $request->user_id;

      $assignedModuleIds = AssignModule::where('company_id', $companyId)->pluck('module_id');
      $groupedModules = Module::whereIn('id', $assignedModuleIds)
         ->where('status', 1)
         ->orderBy('group_order')
         ->get()
         ->groupBy('group_name');

      $userModules = UserModule::where('user_id', $userId)->pluck('module_id')->toArray();

      return response()->json([
         'status'         => 'success',
         'groupedModules' => $groupedModules,
         'userModules'    => $userModules,
      ]);
   }

   /**
    * Update User Modules via Popup
    */
   public function updateUserModules(Request $request): JsonResponse
   {
      $request->validate([
         'user_id'   => 'required|integer|exists:users,id',
         'modules'   => 'required|array|min:1',
         'modules.*' => 'exists:modules,id',
      ]);

      $companyId = auth()->user()->company_id;
      $user = User::where('company_id', $companyId)->findOrFail($request->user_id);

      UserModule::where('user_id', $user->id)->delete();
      foreach ($request->modules as $moduleId) {
         UserModule::create([
            'company_id' => $companyId,
            'user_id'    => $user->id,
            'module_id'  => $moduleId,
         ]);
      }

      return response()->json([
         'status'  => 'success',
         'message' => 'Modules permissions updated successfully.',
      ]);
   }

   public function exportPdf(Request $request)
   {
      $role = $request->get('role', 3);
      $companyId = auth()->user()->company_id;

      $users = User::with('company')
         ->where('company_id', $companyId)
         ->where('role', $role)
         ->get();

      $company = Company::find($companyId);

      $data = [
         'users'        => $users,
         'role_title'   => $role == 2 ? 'Admins Directory' : 'Users Directory',
         'company_name' => $company ? $company->company_name : 'OMSystem Portal',
         'company_logo' => $company ? $company->company_logo : 'assets/img/main-logo.png'
      ];

      $pdf = Pdf::loadView('um.pdf.users_list', $data);
      return $pdf->download(($role == 2 ? 'Admins_List_' : 'Users_List_') . date('Ymd_His') . '.pdf');
   }
}
