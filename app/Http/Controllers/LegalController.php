<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Privacy Policy Page (Meta & Google OAuth Compliant)
     */
    public function privacy()
    {
        return view('legal.privacy');
    }

    /**
     * Terms of Service Page
     */
    public function terms()
    {
        return view('legal.terms');
    }

    /**
     * User Data Deletion Page & Callback (Meta App Review Mandate)
     */
    public function dataDeletion(Request $request)
    {
        // If Meta sends a POST callback with signed_request
        if ($request->isMethod('post')) {
            $confirmationCode = 'del_' . substr(md5(uniqid((string) mt_rand(), true)), 0, 16);
            $statusUrl = url('/data-deletion?code=' . $confirmationCode);

            return response()->json([
                'url'               => $statusUrl,
                'confirmation_code' => $confirmationCode,
            ]);
        }

        // GET request: human-readable data deletion instructions
        $code = $request->query('code');
        return view('legal.data-deletion', compact('code'));
    }
}
