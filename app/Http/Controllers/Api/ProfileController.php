<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\UserContact;
use App\Models\UserRole;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = Profile::firstOrCreate(['id' => $user->id]);
        $profile->setAttribute('email', $user->email);
        $profile->setAttribute('phone', UserContact::where('user_id', $user->id)->value('phone'));
        return response()->json(['data' => $profile]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'display_name' => ['sometimes', 'nullable', 'string', 'max:255'], 'avatar_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:2000'], 'age' => ['sometimes', 'nullable', 'integer', 'min:13', 'max:100'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'], 'account_type' => ['prohibited'],
            'role_selected' => ['prohibited'], 'is_blocked' => ['prohibited'], 'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
        ]);
        $user = $request->user();
        $profile = Profile::firstOrCreate(['id' => $user->id]);
        $phone = $data['phone'] ?? null;
        unset($data['phone']);
        $profile->fill($data)->save();
        if (array_key_exists('phone', $request->all())) {
            if ($phone) UserContact::updateOrCreate(['user_id' => $user->id], ['phone' => $phone]);
            else UserContact::where('user_id', $user->id)->delete();
        }
        return $this->show($request);
    }

    public function contact(Request $request)
    {
        $data = $request->validate(['phone' => ['required', 'string', 'max:30']]);
        $contact = UserContact::updateOrCreate(['user_id' => $request->user()->id], ['phone' => $data['phone']]);
        return response()->json(['data' => $contact]);
    }

    public function becomeHost(Request $request)
    {
        $user = $request->user();
        $user->userRoleByUserId()->firstOrCreate(['role' => 'host']);
        Profile::whereKey($user->id)->update(['account_type' => 'host', 'role_selected' => true]);
        return $this->show($request);
    }

    public function selectRole(Request $request)
    {
        $data = $request->validate(['account_type' => ['required', 'in:traveler,host']]);
        $user = $request->user();
        $profile = Profile::firstOrCreate(['id' => $user->id]);
        abort_if($profile->role_selected && $profile->account_type !== $data['account_type'], 403, 'Your account type has already been selected.');
        $profile->update(['account_type' => $data['account_type'], 'role_selected' => true]);
        $user->userRoleByUserId()->firstOrCreate(['role' => UserRole::forAccountType($data['account_type'])]);
        return $this->show($request);
    }
}
