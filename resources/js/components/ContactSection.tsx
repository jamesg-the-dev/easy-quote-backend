import { Mail, Phone, MessageCircle } from "lucide-react";

interface ContactSectionProps {
  businessEmail: string;
  businessPhone: string;
  hasWhatsApp?: boolean;
  responseTime?: string;
}

export function ContactSection({ 
  businessEmail, 
  businessPhone, 
  hasWhatsApp = false,
  responseTime = "1 business day" 
}: ContactSectionProps) {
  return (
    <div className="px-4 py-6">
      <div className="max-w-2xl mx-auto">
        <div className="bg-white rounded-2xl border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Have questions?</h2>
          
          <div className="space-y-3 mb-4">
            <button 
              onClick={() => window.location.href = `mailto:${businessEmail}`}
              className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-900 rounded-xl border border-gray-200 transition-colors"
            >
              <Mail className="w-5 h-5" />
              <span className="font-medium">Email business</span>
            </button>
            
            <button 
              onClick={() => window.location.href = `tel:${businessPhone}`}
              className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-900 rounded-xl border border-gray-200 transition-colors"
            >
              <Phone className="w-5 h-5" />
              <span className="font-medium">Call business</span>
            </button>

            {hasWhatsApp && (
              <button className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-900 rounded-xl border border-gray-200 transition-colors">
                <MessageCircle className="w-5 h-5" />
                <span className="font-medium">Message on WhatsApp</span>
              </button>
            )}
          </div>

          <p className="text-sm text-gray-500 text-center">
            We usually respond within {responseTime}
          </p>
        </div>
      </div>
    </div>
  );
}
