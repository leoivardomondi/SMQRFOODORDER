<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSetupController extends Controller
{
    public function show(Request $request)
    {
        abort_unless($request->hasValidSignature(), 403, 'This setup link is invalid or has expired.');
        $this->validReset($request);

        return view('adminSetup', ['email' => $request->query('email')]);
    }

    public function update(Request $request)
    {
        abort_unless($request->hasValidSignature(), 403, 'This setup link is invalid or has expired.');
        $reset = $this->validReset($request);
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::withoutGlobalScopes()->where('email', $request->query('email'))->firstOrFail();
        $user->update(['password' => Hash::make($validated['password'])]);
        DB::table('password_resets')->where('email', $reset->email)->delete();

        return redirect('/login')->with('success', 'Your password has been created. You can now log in.');
    }

    private function validReset(Request $request): object
    {
        $reset = DB::table('password_resets')->where('email', $request->query('email'))->first();
        abort_unless($reset && Hash::check((string) $request->query('token'), $reset->token), 403, 'This setup link has already been used or replaced.');
        abort_if(Carbon::parse($reset->created_at)->addHours(24)->isPast(), 403, 'This setup link has expired.');
        return $reset;
    }
}
