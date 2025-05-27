<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        try {
            // Validate payment form
            $validationRules = [
                'payment_method' => 'required|string|in:mpesa,vabu',
                'payment_notes' => 'nullable|string|max:500',
                'payment_confirmed' => 'required|in:on,1',
                'processed_by_user_id' => 'nullable|integer',
                'payment_amount' => 'required|numeric|min:0',
            ];
            
            // Add method-specific validation rules
            if ($request->input('payment_method') === 'mpesa') {
                $validationRules['transaction_code'] = 'nullable|string|min:3|max:12'; // Make transaction code optional
            } else {
                $validationRules['vabu_payment_confirmed'] = 'required|in:on,1';
            }
            
            // Participant ID is optional at this stage as it might be created later
            if ($request->has('participant_id')) {
                $validationRules['participant_id'] = 'required|integer|exists:participants,id';
            }
            
            $validated = $request->validate($validationRules);
            
            // Get current registration data
            $data = Session::get('registration_data', []);
            
            // Check if necessary data exists
            if (!isset($data['category'])) {
                Log::error('Payment processing error: Registration data is incomplete');
                return redirect()->route('usher.registration.step1')
                    ->with('error', 'Registration data is incomplete. Please start over.');
            }
            
            // Set payment amount based on category and type
            switch ($data['category']) {
                case 'exhibitor':
                    $data['payment_amount'] = Participant::EXHIBITOR_FEE;
                    break;
                case 'presenter':
                    if (!isset($data['presenter_type'])) {
                        return redirect()->route('usher.registration.step2')
                            ->with('error', 'Presenter type is missing. Please complete the details step.');
                    }
                    
                    switch ($data['presenter_type']) {
                        case 'non_student':
                            $data['payment_amount'] = Participant::PRESENTER_NON_STUDENT_FEE;
                            break;
                        case 'student':
                            $data['payment_amount'] = Participant::PRESENTER_STUDENT_FEE;
                            break;
                        case 'international':
                            $data['payment_amount'] = Participant::PRESENTER_INTERNATIONAL_FEE;
                            break;
                        default:
                            return redirect()->route('usher.registration.step2')
                                ->with('error', 'Invalid presenter type. Please complete the details step again.');
                    }
                    break;
                case 'general':
                    if (!isset($data['eligible_days'])) {
                        return redirect()->route('usher.registration.step2')
                            ->with('error', 'Number of eligible days is missing. Please complete the details step.');
                    }
                    
                    $days = (int) $data['eligible_days'];
                    $data['payment_amount'] = match($days) {
                        1 => 3000,
                        2 => 6000,
                        3 => 9000,
                        default => 3000,
                    };
                    break;
                default:
                    return redirect()->route('usher.registration.step2')
                        ->with('error', 'Invalid category. Please start the registration process again.');
            }
            
            // Update payment information
            if ($validated['payment_method'] === 'mpesa') {
                $transactionCode = $validated['transaction_code'] ?? null;
                
                // Only validate length if a transaction code was provided
                if (!empty($transactionCode) && strlen($transactionCode) > 20) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['transaction_code' => 'Transaction code is too long. Maximum 20 characters.']);
                }
                
                $data['payment_status'] = 'Paid via M-Pesa';
                
                // Generate a placeholder code if none was provided
                if (empty($transactionCode)) {
                    $transactionCode = 'MPESA-' . strtoupper(substr(md5(uniqid()), 0, 8));
                    $data['payment_notes'] = 'M-Pesa Payment (No transaction code provided)' . "\n" . ($validated['payment_notes'] ?? '');
                } else {
                    $data['payment_notes'] = 'M-Pesa Transaction: ' . $transactionCode . "\n" . ($validated['payment_notes'] ?? '');
                }
                
                $data['transaction_code'] = $transactionCode; // Store for Payment model
            } else {
                // Ensure Vabu payment is confirmed
                if (!isset($validated['vabu_payment_confirmed'])) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['vabu_payment_confirmed' => 'You must confirm that payment was made via Vabu.']);
                }
                
                $data['payment_status'] = 'Paid via Vabu';
                $data['payment_notes'] = $validated['payment_notes'] ?? '';
                $data['transaction_code'] = 'VABU-' . time(); // Generate a reference for Vabu payments
            }
            
            $data['payment_confirmed'] = true;
            $data['processed_by_user_id'] = $validated['processed_by_user_id'] ?? Auth::id(); // Track who processed the payment
            
            // For debugging and auditing purposes
            Log::info('Payment processed successfully', [
                'user_id' => Auth::id(),
                'method' => $validated['payment_method'],
                'category' => $data['category'],
                'amount' => $data['payment_amount'] ?? 0,
                'transaction_code' => $data['transaction_code'] ?? null
            ]);
            
            // Update session data
            Session::put('registration_data', $data);
            
            // Attempt to create a preliminary payment record in the database
            try {
                // Only create the payment record if we have a participant_id (rare cases)
                if (isset($data['participant_id'])) {
                    Payment::create([
                        'participant_id' => $data['participant_id'],
                        'amount' => $data['payment_amount'] ?? 0,
                        'payment_method' => $validated['payment_method'],
                        'transaction_code' => $data['transaction_code'],
                        'notes' => $data['payment_notes'] ?? null,
                        'processed_by_user_id' => $data['processed_by_user_id'],
                        'payment_confirmed' => true
                    ]);
                }
            } catch (\Exception $e) {
                // Just log the error but don't interrupt the flow
                Log::warning('Could not create preliminary payment record: ' . $e->getMessage());
            }
            
            return redirect()->route('usher.registration.step3')
                ->with('success', 'Payment information recorded successfully.');
                
        } catch (\Exception $e) {
            // Log the error with detailed information
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'method' => $request->input('payment_method'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while processing the payment. Please try again.');
        }
    }
}
