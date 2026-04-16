import { Download, Share2, FileText, ChevronDown } from "lucide-react";
import { useState } from "react";

interface AdditionalFeaturesProps {
  businessMessage?: string;
  termsAndConditions: string;
}

export function AdditionalFeatures({ businessMessage, termsAndConditions }: AdditionalFeaturesProps) {
  const [isTermsExpanded, setIsTermsExpanded] = useState(false);

  const handleDownloadPDF = () => {
    // Mock download functionality
    alert("PDF download would start here");
  };

  const handleShare = async () => {
    if (navigator.share) {
      try {
        await navigator.share({
          title: "Quote",
          text: "Check out this quote",
          url: window.location.href,
        });
      } catch (err) {
        console.log("Share cancelled");
      }
    } else {
      // Fallback: copy to clipboard
      navigator.clipboard.writeText(window.location.href);
      alert("Link copied to clipboard!");
    }
  };

  return (
    <div className="px-4 py-6">
      <div className="max-w-2xl mx-auto space-y-4">
        {/* Business Message */}
        {businessMessage && (
          <div className="bg-blue-50 border border-blue-200 rounded-2xl p-5">
            <div className="flex items-start gap-3">
              <FileText className="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
              <div>
                <p className="text-sm font-medium text-blue-900 mb-1">Message from business</p>
                <p className="text-sm text-blue-800">{businessMessage}</p>
              </div>
            </div>
          </div>
        )}

        {/* Action Buttons */}
        <div className="bg-white rounded-2xl border border-gray-200 p-4">
          <div className="grid grid-cols-2 gap-3">
            <button
              onClick={handleDownloadPDF}
              className="flex items-center justify-center gap-2 px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-900 rounded-xl border border-gray-200 transition-colors"
            >
              <Download className="w-4 h-4" />
              <span className="text-sm font-medium">Download PDF</span>
            </button>
            
            <button
              onClick={handleShare}
              className="flex items-center justify-center gap-2 px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-900 rounded-xl border border-gray-200 transition-colors"
            >
              <Share2 className="w-4 h-4" />
              <span className="text-sm font-medium">Share Quote</span>
            </button>
          </div>
        </div>

        {/* Terms & Conditions */}
        <div className="bg-white rounded-2xl border border-gray-200 overflow-hidden">
          <button
            onClick={() => setIsTermsExpanded(!isTermsExpanded)}
            className="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition-colors"
          >
            <span className="font-medium text-gray-900">Terms & Conditions</span>
            <ChevronDown
              className={`w-5 h-5 text-gray-500 transition-transform ${
                isTermsExpanded ? "rotate-180" : ""
              }`}
            />
          </button>
          
          {isTermsExpanded && (
            <div className="px-5 pb-5 pt-0 border-t border-gray-100">
              <div className="prose prose-sm max-w-none">
                <p className="text-sm text-gray-600 whitespace-pre-line">
                  {termsAndConditions}
                </p>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
