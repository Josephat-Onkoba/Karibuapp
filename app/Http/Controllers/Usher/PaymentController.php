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
        if (!isset($data['full_name']) || !isset($data['category'])) {
            return redirect()->route('usher.registration.step1');
        }
        
        // Check if category requires payment
        if (!in_array($data['category'], ['general', 'exhibitor', 'presenter'])) {
            return redirect()->route('usher.registration.step2');
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
            'payment_method' => 'required|string|in:mpesa,vabu',
            'transaction_code' => 'required_if:payment_method,mpesa|nullable|string|max:50',
            'payment_notes' => 'nullable|string|max:500',
        ]);
        
        // Get current registration data
        $data = Session::get('registration_data', []);
        
        // Set payment amount based on category and type
        switch ($data['category']) {
            case 'exhibitor':
                $data['payment_amount'] = 30000;
                break;
            case 'presenter':
                switch ($data['presenter_type']) {
                    case 'non_student':
                        $data['payment_amount'] = 6000;
                        break;
                    case 'student':
                        $data['payment_amount'] = 4000;
                        break;
                    case 'international':
                        $data['payment_amount'] = 100;
                        break;
                }
                break;
        }
        
        // Update payment information
        if ($validated['payment_method'] === 'mpesa') {
            $data['payment_status'] = 'Paid via M-Pesa';
            $data['payment_notes'] = 'M-Pesa Transaction: ' . $validated['transaction_code'] . "\n" . ($validated['payment_notes'] ?? '');
        } else {
            $data['payment_status'] = 'Paid via Vabu';
            $data['payment_notes'] = $validated['payment_notes'] ?? '';
        }
        
        $data['payment_confirmed'] = true;
        
        // Update session data
        Session::put('registration_data', $data);
        
        return redirect()->route('usher.registration.step3')
            ->with('success', 'Payment information recorded successfully.');
    }
}
