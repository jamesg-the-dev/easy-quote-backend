import { useState } from "react";
import { QuoteHeader } from "./components/QuoteHeader";
import { QuoteSummary } from "./components/QuoteSummary";
import { LineItems } from "./components/LineItems";
import { ContactSection } from "./components/ContactSection";
import { StatusTimeline } from "./components/StatusTimeline";
import { AdditionalFeatures } from "./components/AdditionalFeatures";
import { ActionBar } from "./components/ActionBar";

export interface LineItem {
  id: string;
  name: string;
  description?: string;
  quantity: number;
  unitPrice: number;
  subtotal: number;
}

export interface QuoteData {
  businessName: string;
  quoteNumber: string;
  dateIssued: string | Date;
  expiryDate: string | Date;
  totalAmount: number;
  currency: string;
  businessEmail: string;
  businessPhone: string;
  businessMessage?: string;
  termsAndConditions: string;
  lineItems: LineItem[];
  hasWhatsApp?: boolean;
}

interface InvoiceAppProps {
  quoteData: QuoteData;
}

export default function InvoiceApp({ quoteData }: InvoiceAppProps) {
  const [quoteStatus, setQuoteStatus] = useState<"sent" | "viewed" | "accepted">("viewed");

  // Convert string dates to Date objects if necessary
  const dateIssued = typeof quoteData.dateIssued === 'string' 
    ? new Date(quoteData.dateIssued) 
    : quoteData.dateIssued;
    
  const expiryDate = typeof quoteData.expiryDate === 'string' 
    ? new Date(quoteData.expiryDate) 
    : quoteData.expiryDate;

  const handleAccept = () => {
    setQuoteStatus("accepted");
    // Call API to update the quote status
    fetch(`/api/quotes/${quoteData.quoteNumber}/accept`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
      },
    })
      .then(response => {
        if (response.ok) {
          alert("✅ Quote accepted! We'll send you a confirmation email shortly.");
        }
      })
      .catch(error => {
        console.error('Error accepting quote:', error);
        alert("Failed to accept quote. Please try again.");
      });
  };

  const handleRequestChanges = () => {
    // Call API to request changes
    fetch(`/api/quotes/${quoteData.quoteNumber}/request-changes`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
      },
    })
      .then(response => {
        if (response.ok) {
          alert("📝 Change request submitted. The business will review your request.");
        }
      })
      .catch(error => console.error('Error requesting changes:', error));
  };

  const handleDecline = () => {
    if (confirm("Are you sure you want to decline this quote?")) {
      // Call API to decline quote
      fetch(`/api/quotes/${quoteData.quoteNumber}/decline`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
        },
      })
        .then(response => {
          if (response.ok) {
            alert("Quote declined. We'll notify the business.");
            // Optionally redirect
            window.location.href = '/';
          }
        })
        .catch(error => console.error('Error declining quote:', error));
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 pb-32">
      <QuoteHeader
        businessName={quoteData.businessName}
        status={quoteStatus === "accepted" ? "approved" : "awaiting"}
      />

      <QuoteSummary
        quoteNumber={quoteData.quoteNumber}
        dateIssued={dateIssued}
        expiryDate={expiryDate}
        totalAmount={quoteData.totalAmount}
        currency={quoteData.currency}
      />

      <LineItems
        items={quoteData.lineItems}
        currency={quoteData.currency}
      />

      <StatusTimeline currentStatus={quoteStatus} />

      <AdditionalFeatures
        businessMessage={quoteData.businessMessage}
        termsAndConditions={quoteData.termsAndConditions}
      />

      <ContactSection
        businessEmail={quoteData.businessEmail}
        businessPhone={quoteData.businessPhone}
        hasWhatsApp={quoteData.hasWhatsApp}
      />

      <ActionBar
        onAccept={handleAccept}
        onRequestChanges={handleRequestChanges}
        onDecline={handleDecline}
      />
    </div>
  );
}
