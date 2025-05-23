<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    /**
     * Show the payment form for general category
     */
    public function showPaymentForm()
    {
        // Check if step 2 is completed
        $data = Session::get('registration_data', []);
        if (!isset($data['full_name']) || !isset($data['category']) || $data['category'] !== 'general') {
            return redirect()->route('usher.registration.step1');
        }
        
        // Ensure payment status is "Not Paid"
        if (!isset($data['payment_status']) || $data['payment_status'] !== 'Not Paid') {
            return redirect()->route('usher.registration.step2');
        }
        
        return view('usher.registration.steps.payment', compact('data'));
    }
    
    /**
     * Process the payment form
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:m_pesa,vabu',
            'transaction_code' => 'required_if:payment_method,m_pesa|nullable|string|max:50',
            'payment_confirmed' => 'nullable|boolean',
        ]);
        
        // Get current registration data
        $data = Session::get('registration_data', []);
        
        // Update payment information
        if ($validated['payment_method'] === 'm_pesa') {
            $data['payment_status'] = 'Paid via M-Pesa';
            $data['payment_notes'] = 'M-Pesa Transaction: ' . $validated['transaction_code'] . "\n" . ($data['payment_notes'] ?? '');
        } else {
            $data['payment_status'] = 'Paid via Vabu';
        }
        
        $data['payment_confirmed'] = isset($validated['payment_confirmed']) && $validated['payment_confirmed'] ? true : false;
        
        // Update session data
        Session::put('registration_data', $data);
        
        return redirect()->route('usher.registration.step3')
            ->with('success', 'Payment information recorded successfully.');
    }
}
