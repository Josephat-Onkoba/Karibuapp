<?php

namespace App\Http\Controllers\Usher;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RegistrationController extends Controller
{
    /**
     * Show the additional day payment form
     */
    public function showAdditionalDayPayment(Request $request)
    {
        $paymentData = Session::get('additional_day_payment');
        
        if (!$paymentData) {
            return redirect()->route('usher.check-in')
                ->with('error', 'No additional day payment information found.');
        }
        
        $participant = Participant::find($paymentData['participant_id']);
        
        if (!$participant) {
            return redirect()->route('usher.check-in')
                ->with('error', 'Participant not found.');
        }
        
        // Store participant info in session for the view
        Session::put('participant', [
            'presenter_type' => $participant->presenter_type
        ]);
        
        return view('usher.registration.additional-day-payment');
    }
    
    /**
     * Process the additional day payment
     */
    public function processAdditionalDayPayment(Request $request)
    {
        $paymentData = Session::get('additional_day_payment');
        
        if (!$paymentData) {
            return redirect()->route('usher.check-in')
                ->with('error', 'No additional day payment information found.');
        }
        
        $participant = Participant::find($paymentData['participant_id']);
        
        if (!$participant) {
            return redirect()->route('usher.check-in')
                ->with('error', 'Participant not found.');
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:mpesa,vabu',
            'mpesa_code' => 'required_if:payment_method,mpesa|string|max:255',
            'vabu_payment_confirmed' => 'required_if:payment_method,vabu|accepted',
            'payment_notes' => 'nullable|string|max:1000'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create payment record
            $payment = new Payment();
            $payment->participant_id = $participant->id;
            $payment->amount = $paymentData['required_payment'];
            $payment->payment_method = $validated['payment_method'];
            $payment->transaction_code = $validated['payment_method'] === 'mpesa' ? $validated['mpesa_code'] : null;
            $payment->notes = $validated['payment_notes'];
            $payment->processed_by_user_id = Auth::id();
            $payment->payment_confirmed = true;
            $payment->save();
            
            // Update participant's eligible days
            $participant->eligible_days = ($participant->eligible_days ?? 0) + 1;
            $participant->save();
            
            DB::commit();
            
            // Clear the additional day payment session data
            Session::forget(['additional_day_payment', 'participant']);
            
            // Redirect based on return route
            $redirectRoute = match($paymentData['return_to']) {
                'my-registrations' => 'usher.registration.my-registrations',
                'participant_view' => 'usher.participant.view',
                default => 'usher.check-in'
            };
            
            if ($paymentData['return_to'] === 'participant_view' && isset($paymentData['participant_id_redirect'])) {
                return redirect()->route($redirectRoute, $paymentData['participant_id_redirect'])
                    ->with('success', 'Additional day payment processed successfully. You can now check in the participant.');
            }
            
            return redirect()->route($redirectRoute)
                ->with('success', 'Additional day payment processed successfully. You can now check in the participant.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Additional day payment processing failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to process payment. ' . $e->getMessage());
        }
    }
} 