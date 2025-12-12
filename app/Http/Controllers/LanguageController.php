<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Change user language preference
     */
    public function changeLanguage(Request $request)
    {
        $request->validate([
            'language' => 'required|in:fr,es,en',
        ]);

        $user = Auth::user();
        $user->language = $request->language;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Language changed successfully',
            'language' => $request->language
        ]);
    }
}
