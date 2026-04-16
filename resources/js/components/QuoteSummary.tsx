import { Calendar, AlertCircle } from "lucide-react";
import { differenceInDays } from "date-fns";

interface QuoteSummaryProps {
  quoteNumber: string;
  dateIssued: Date;
  expiryDate: Date;
  totalAmount: number;
  currency?: string;
}

export function QuoteSummary({ 
  quoteNumber, 
  dateIssued, 
  expiryDate, 
  totalAmount, 
  currency = "USD" 
}: QuoteSummaryProps) {
  const daysUntilExpiry = differenceInDays(expiryDate, new Date());
  const isCloseToExpiry = daysUntilExpiry >= 0 && daysUntilExpiry <= 7;

  const formatDate = (date: Date) => {
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency,
      minimumFractionDigits: 2,
    }).format(amount);
  };

  return (
    <div className="px-4 pt-4 pb-0">
      <div className="max-w-2xl mx-auto">
        {isCloseToExpiry && (
          <div className="mb-4 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
            <AlertCircle className="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
            <div>
              <p className="text-sm font-medium text-amber-900">
                {daysUntilExpiry === 0 
                  ? "This quote expires today" 
                  : `This quote expires in ${daysUntilExpiry} ${daysUntilExpiry === 1 ? 'day' : 'days'}`}
              </p>
              <p className="text-sm text-amber-700 mt-0.5">
                Please review and accept before {formatDate(expiryDate)}
              </p>
            </div>
          </div>
        )}
        
        <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
          <div className="flex items-center justify-between mb-6">
            <div>
              <p className="text-sm text-gray-500 mb-1">Quote Number</p>
              <p className="font-semibold text-gray-900">{quoteNumber}</p>
            </div>
            <div className="flex items-center gap-2 text-gray-500">
              <Calendar className="w-4 h-4" />
              <span className="text-sm">{formatDate(dateIssued)}</span>
            </div>
          </div>

          <div className="border-t border-gray-100 pt-6">
            <div className="flex items-baseline justify-between">
              <div>
                <p className="text-sm text-gray-500 mb-2">Total Amount</p>
                <p className="text-5xl font-bold text-gray-900 tracking-tight">
                  {formatCurrency(totalAmount)}
                </p>
              </div>
            </div>
            
            <div className="mt-6 pt-6 border-t border-gray-100">
              <div className="flex items-center justify-between text-sm">
                <span className="text-gray-500">Valid until</span>
                <span className="font-medium text-gray-900">{formatDate(expiryDate)}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
