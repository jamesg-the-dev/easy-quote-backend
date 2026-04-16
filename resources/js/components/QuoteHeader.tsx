import { Building2 } from "lucide-react";

interface QuoteHeaderProps {
  businessName: string;
  businessLogo?: string;
  status: "sent" | "viewed" | "approved" | "awaiting";
}

export function QuoteHeader({ businessName, businessLogo, status }: QuoteHeaderProps) {
  const getStatusConfig = () => {
    switch (status) {
      case "approved":
        return { label: "Approved", className: "bg-green-50 text-green-700 border-green-200" };
      case "viewed":
        return { label: "Viewed", className: "bg-blue-50 text-blue-700 border-blue-200" };
      case "awaiting":
        return { label: "Awaiting Approval", className: "bg-amber-50 text-amber-700 border-amber-200" };
      default:
        return { label: "Quote Sent", className: "bg-gray-50 text-gray-700 border-gray-200" };
    }
  };

  const statusConfig = getStatusConfig();

  return (
    <header className="bg-white border-b border-gray-200 px-4 py-6">
      <div className="max-w-2xl mx-auto">
        <div className="flex items-center justify-between mb-4">
          <div className="flex items-center gap-3">
            {businessLogo ? (
              <img src={businessLogo} alt={businessName} className="w-12 h-12 rounded-lg object-cover" />
            ) : (
              <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                <Building2 className="w-6 h-6 text-white" />
              </div>
            )}
            <h1 className="text-xl font-semibold text-gray-900">{businessName}</h1>
          </div>
        </div>
        <div className="flex items-center">
          <span className={`inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border ${statusConfig.className}`}>
            {statusConfig.label}
          </span>
        </div>
      </div>
    </header>
  );
}
