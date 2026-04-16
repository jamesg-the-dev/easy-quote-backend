import { createRoot } from "react-dom/client";
import InvoiceApp from "./Invoice";
import "./styles/index.css";

// Get quote data from data attribute on the root element
const rootElement = document.getElementById("invoice-app") as HTMLDivElement;
const quoteData = rootElement?.dataset.quote ? JSON.parse(rootElement.dataset.quote) : null;

if (!quoteData) {
  console.error("Quote data not found in page data attribute");
} else {
  createRoot(rootElement).render(<InvoiceApp quoteData={quoteData} />);
}
