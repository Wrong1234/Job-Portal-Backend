<?php

namespace App\Http\Controllers;

use Stripe\Webhook;
use Stripe\StripeClient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;

class PaymentController extends Controller
{
    public function createCheckout(Request $request)
    {

        $request->validate([
            'job_id'  => 'required|exists:jobs,id',         
            'amount'  => 'required|numeric|min:1',         
            'upload_cv' => 'required|file|max:20480',       
        ]);

        $user = Auth::user();
        $jobId = $request->job_id;
        $amount = $request->amount; 
        $cv_path = $request->upload_cv;

        $stripe_payment_id = rand(100000, 999999);
        $payment = Payment::create([
            'user_id' => $user->id,
            'job_id' => $jobId,
            'amount' => $amount,
            'status' => 'pending',
            'stripe_payment_id' => $stripe_payment_id,
        ]);

        $stripe = new StripeClient(env('STRIPE_SECRET'));

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => env('STRIPE_CURRENCY', 'usd'),
                    'product_data' => ['name' => "Job application fee"],
                    'unit_amount' => (int) (100 * 100), 
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'job_id' => $jobId,
                'upload_cv' => $cv_path,
            ],
            'success_url' => route('api.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url('/jobs'),
        ]);

        // 3) update local payment with session id
        $payment->update(['stripe_payment_id' => $session->id]);

        return response()->json(['url' => $session->url]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        return response()->json([
                'message' => 'Payment completed successfully',
                'session_id' => $sessionId
            ]);
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        $event = Webhook::constructEvent($payload, $sigHeader, $secret);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $paymentId = $session->metadata->payment_id ?? null;

            if ($paymentId) {
                $payment = Payment::find($paymentId);
                if ($payment) {
                    $payment->update([
                        'status' => 'success',
                        'stripe_payment_intent' => $session->payment_intent ?? null,
                    ]);

                     $application = Application::create([
                        'job_id' => $session->metadata->job_id ?? null,
                        'user_id' => $session->metadata->user_id ?? null,
                        'payment_id' => $payment->id,
                        'status' => 'pending',
                        'cv_path' => $session->metadata->upload_cv ?? null,
                        'applied_at' => now(),
                    ]);

                    $payment->update([
                        'application_id' => $application->id,
                    ]);
                }
            }
             return response('Payment successful', 200);
        }

        return response('Event ignored', 200);
    }
}

