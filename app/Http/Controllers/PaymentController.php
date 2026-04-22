<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Show payment form for an order
     */
    public function create(Order $order): View
    {
        // Check if user is the buyer of this order
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        // Check if payment already exists
        $existingPayment = $order->payment;
        if ($existingPayment) {
            return redirect()->route('buyer.payments.show', $existingPayment);
        }

        // Get farmer bank information
        $farmer = $order->orderItems->first()->farmer;

        return view('payments.create', [
            'order' => $order,
            'farmer' => $farmer,
        ]);
    }

    /**
     * Store payment receipt
     */
    public function store(Request $request, Order $order): RedirectResponse
    {
        // Check if user is the buyer of this order
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:bank,mobile_money,cash',
            'transaction_reference' => 'required|string|max:255',
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        // Upload receipt image
        if ($request->hasFile('receipt_image')) {
            $image = $request->file('receipt_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/receipts', $imageName);
            $validated['receipt_image'] = $imageName;
        }

        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'buyer_id' => Auth::id(),
            'farmer_id' => $order->orderItems->first()->farmer_id,
            'amount' => $order->total_amount,
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'transaction_reference' => $validated['transaction_reference'],
            'receipt_image' => $validated['receipt_image'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('buyer.payments.show', $payment)
            ->with('success', 'Payment receipt uploaded successfully. Waiting for farmer verification.');
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment): View
    {
        // Check if user is buyer or farmer of this payment
        if ($payment->buyer_id !== Auth::id() && $payment->farmer_id !== Auth::id()) {
            abort(403);
        }

        return view('payments.show', [
            'payment' => $payment,
        ]);
    }

    /**
     * Verify payment (for farmers)
     */
    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        // Check if user is the farmer of this payment
        if ($payment->farmer_id !== Auth::id()) {
            abort(403);
        }

        // Check if payment is pending
        if (!$payment->isPending()) {
            return back()->with('error', 'This payment has already been processed.');
        }

        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
            'rejection_reason' => 'required_if:action,reject|string|max:500',
        ]);

        if ($validated['action'] === 'verify') {
            $payment->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);

            // Update order status to confirmed
            $payment->order->update(['status' => 'confirmed']);

            return back()->with('success', 'Payment verified successfully!');
        } else {
            $payment->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            return back()->with('success', 'Payment rejected. Buyer will need to upload a new receipt.');
        }
    }

    /**
     * Show buyer's payments
     */
    public function buyerPayments(): View
    {
        $payments = Payment::where('buyer_id', Auth::id())
            ->with('order', 'farmer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payments.buyer.index', [
            'payments' => $payments,
        ]);
    }

    /**
     * Show farmer's payments
     */
    public function farmerPayments(): View
    {
        $payments = Payment::where('farmer_id', Auth::id())
            ->with('order', 'buyer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payments.farmer.index', [
            'payments' => $payments,
        ]);
    }
}
