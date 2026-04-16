import { Check } from "lucide-react";

type TimelineStatus = "sent" | "viewed" | "accepted";

interface StatusTimelineProps {
  currentStatus: TimelineStatus;
}

export function StatusTimeline({ currentStatus }: StatusTimelineProps) {
  const steps = [
    { key: "sent", label: "Sent" },
    { key: "viewed", label: "Viewed" },
    { key: "accepted", label: "Accepted" },
  ];

  const getStepIndex = (status: TimelineStatus) => {
    return steps.findIndex(step => step.key === status);
  };

  const currentIndex = getStepIndex(currentStatus);

  return (
    <div className="px-4 py-6">
      <div className="max-w-2xl mx-auto">
        <div className="bg-white rounded-2xl border border-gray-200 p-6">
          <h3 className="text-sm font-medium text-gray-900 mb-6">Quote Status</h3>
          
          <div className="relative">
            {/* Progress line */}
            <div className="absolute top-5 left-5 right-5 h-0.5 bg-gray-200" aria-hidden="true">
              <div 
                className="h-full bg-blue-600 transition-all duration-500"
                style={{ width: `${(currentIndex / (steps.length - 1)) * 100}%` }}
              />
            </div>

            {/* Steps */}
            <div className="relative flex justify-between">
              {steps.map((step, index) => {
                const isCompleted = index <= currentIndex;
                const isCurrent = index === currentIndex;

                return (
                  <div key={step.key} className="flex flex-col items-center">
                    <div
                      className={`w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 ${
                        isCompleted
                          ? "bg-blue-600 border-blue-600"
                          : "bg-white border-gray-300"
                      }`}
                    >
                      {isCompleted && (
                        <Check className="w-5 h-5 text-white" />
                      )}
                    </div>
                    <span
                      className={`mt-2 text-xs font-medium ${
                        isCurrent ? "text-gray-900" : isCompleted ? "text-gray-700" : "text-gray-500"
                      }`}
                    >
                      {step.label}
                    </span>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
