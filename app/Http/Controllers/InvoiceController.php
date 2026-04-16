<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display the invoice view
     */
    public function show($quoteNumber)
    {
        // TODO: Fetch the quote from your database
        // For now, using mock data - replace with actual database query
        $quoteData = $this->getMockQuote($quoteNumber);
        
        if (!$quoteData) {
            abort(404, 'Quote not found');
        }

        return view('invoice', [
            'quoteData' => $quoteData,
            'quote' => (object)$quoteData, // For Blade access
        ]);
    }

    /**
     * Handle quote acceptance
     */
    public function accept($quoteNumber)
    {
        // TODO: Update quote status in database
        // Send confirmation email to client
        
        return response()->json([
            'success' => true,
            'message' => 'Quote accepted successfully',
        ]);
    }

    /**
     * Handle change requests
     */
    public function requestChanges($quoteNumber, Request $request)
    {
        // TODO: Create a change request record
        // Notify the business of the change request
        
        $changes = $request->validate([
            'message' => 'nullable|string',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Change request submitted',
        ]);
    }

    /**
     * Handle quote decline
     */
    public function decline($quoteNumber)
    {
        // TODO: Update quote status to declined
        // Notify the business
        
        return response()->json([
            'success' => true,
            'message' => 'Quote declined',
        ]);
    }

    /**
     * Mock quote data - REPLACE THIS WITH DATABASE QUERY
     */
    private function getMockQuote($quoteNumber)
    {
        $quotes = [
            'QT-2026-0847' => [
                'businessName' => 'Acme Design Studio',
                'quoteNumber' => 'QT-2026-0847',
                'dateIssued' => now()->subDays(6),
                'expiryDate' => now()->addDays(4),
                'totalAmount' => 4850.00,
                'currency' => 'USD',
                'businessEmail' => 'hello@acmedesign.com',
                'businessPhone' => '+1 (555) 123-4567',
                'hasWhatsApp' => true,
                'businessMessage' => 'Thank you for your interest! We\'re excited to work with you on this project. We\'ve included a 10% discount for early acceptance within the next 5 days.',
                'termsAndConditions' => "Payment Terms: 50% deposit required upon acceptance, remaining 50% due upon project completion.\n\nDelivery Timeline: Project will be completed within 4-6 weeks from deposit payment.\n\nRevisions: Includes up to 3 rounds of revisions. Additional revisions will be billed at \$150/hour.\n\nCancellation Policy: Deposits are non-refundable. If project is cancelled after work has begun, client will be billed for work completed to date.\n\nThis quote is valid for 10 days from the issue date. Prices and availability subject to change after expiry.",
                'lineItems' => [
                    [
                        'id' => '1',
                        'name' => 'Brand Identity Design',
                        'description' => 'Complete brand identity including logo, color palette, and typography system',
                        'quantity' => 1,
                        'unitPrice' => 2500.00,
                        'subtotal' => 2500.00,
                    ],
                    [
                        'id' => '2',
                        'name' => 'Website Design (5 pages)',
                        'description' => 'Custom website design for Home, About, Services, Portfolio, and Contact pages',
                        'quantity' => 5,
                        'unitPrice' => 350.00,
                        'subtotal' => 1750.00,
                    ],
                    [
                        'id' => '3',
                        'name' => 'Social Media Templates',
                        'description' => 'Instagram and LinkedIn post templates (10 designs)',
                        'quantity' => 10,
                        'unitPrice' => 75.00,
                        'subtotal' => 750.00,
                    ],
                ],
            ],
        ];

        return $quotes[$quoteNumber] ?? null;
    }
}
