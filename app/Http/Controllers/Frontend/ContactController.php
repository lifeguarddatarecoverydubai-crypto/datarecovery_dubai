<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Show the contact page.
     */
    public function show(): View
    {
        $page = Page::where('slug', 'contact-us')->first();

        return view('frontend.contact', compact('page'));
    }

    /**
     * Handle the contact form submission.
     */
    public function submit(Request $request): RedirectResponse
    {
        // 1. Silent Bot Honeypot: Discard automated spam bots cleanly
        if ($request->filled('_hp_website') || $request->filled('bot_check')) {
            return back()->with('success', 'Thank you for contacting LifeGuard Data Recovery! Your evaluation request has been received.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'device_type' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'string', 'max:100'],
            'symptoms' => ['nullable', 'string', 'max:255'],
            'emirate' => ['nullable', 'string', 'max:100'],
            'device_model' => ['nullable', 'string', 'max:255'],
            'pickup_requested' => ['nullable', 'string', 'max:20'],
            'type' => ['nullable', 'string', 'max:50'],
        ]);

        $messageParts = [];
        if (! empty($validated['device_type'])) {
            $messageParts[] = 'Storage Media Type: '.strip_tags($validated['device_type']);
        }
        if (! empty($validated['capacity'])) {
            $messageParts[] = 'Storage Capacity: '.strip_tags($validated['capacity']);
        }
        if (! empty($validated['symptoms'])) {
            $messageParts[] = 'Reported Failure Symptoms: '.strip_tags($validated['symptoms']);
        }
        if (! empty($validated['emirate'])) {
            $messageParts[] = 'Location / Branch: '.strip_tags($validated['emirate']);
        }
        if (! empty($validated['device_model'])) {
            $messageParts[] = 'Brand & Model / Serial: '.strip_tags($validated['device_model']);
        }
        if (! empty($validated['pickup_requested']) && in_array(strtolower($validated['pickup_requested']), ['yes', '1', 'true', 'on'])) {
            $messageParts[] = 'Courier Service: Free Courier Pickup Requested';
        }
        if (! empty($validated['message'])) {
            $messageParts[] = 'Incident Description: '.strip_tags($validated['message']);
        }

        $fullMessage = ! empty($messageParts) ? implode("\n\n", $messageParts) : (! empty($validated['message']) ? strip_tags($validated['message']) : null);

        $type = $validated['type'] ?? 'evaluation';

        Inquiry::create([
            'name' => strip_tags(trim($validated['name'])),
            'email' => trim($validated['email']),
            'phone' => ! empty($validated['phone']) ? strip_tags(trim($validated['phone'])) : null,
            'company' => ! empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
            'subject' => strip_tags(trim($validated['subject'])),
            'message' => $fullMessage,
            'type' => $type,
            'is_read' => false,
        ]);

        return back()->with('success', 'Thank you for contacting LifeGuard Data Recovery! Your emergency evaluation request has been registered. Our senior cleanroom engineer will reach out to you within 15 minutes.');
    }
}
