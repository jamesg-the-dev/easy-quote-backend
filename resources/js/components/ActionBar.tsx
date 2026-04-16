import { Check, MessageSquare, X } from "lucide-react";
import { useState } from "react";

interface ActionBarProps {
  onAccept: () => void;
  onRequestChanges: () => void;
  onDecline: () => void;
}

export function ActionBar({ onAccept, onRequestChanges, onDecline }: ActionBarProps) {
  const [isProcessing, setIsProcessing] = useState(false);

  const handleAccept = async () => {
    setIsProcessing(true);
    // Simulate API call
    setTimeout(() => {
      onAccept();
      setIsProcessing(false);
    }, 1000);
  };

  return (
    <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-4 shadow-lg z-50">
      <div className="max-w-2xl mx-auto space-y-3">
        <button
          onClick={handleAccept}
          disabled={isProcessing}
          className="w-full flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-semibold shadow-lg shadow-green-600/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <Check className="w-5 h-5" />
          <span>{isProcessing ? "Processing..." : "Accept Quote"}</span>
        </button>
        
        <div className="grid grid-cols-2 gap-3">
          <button
            onClick={onRequestChanges}
            className="flex items-center justify-center gap-2 px-4 py-3 bg-white hover:bg-gray-50 text-gray-900 rounded-xl border border-gray-300 font-medium transition-colors"
          >
            <MessageSquare className="w-4 h-4" />
            <span className="text-sm">Request Changes</span>
          </button>
          
          <button
            onClick={onDecline}
            className="flex items-center justify-center gap-2 px-4 py-3 bg-white hover:bg-gray-50 text-red-600 rounded-xl border border-gray-300 font-medium transition-colors"
          >
            <X className="w-4 h-4" />
            <span className="text-sm">Decline</span>
          </button>
        </div>
      </div>
    </div>
  );
}
