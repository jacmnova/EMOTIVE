"use client";

/**
 * Carga Quill + react-quill en un solo chunk para evitar ChunkLoadError
 * (evitar import("quill").then(() => import("react-quill")) que genera _next/undefined).
 */
import Quill from "quill";
import ReactQuill from "react-quill";

// Customizar iconos y fuentes antes de que se monte el editor
const icons = Quill.import("ui/icons") as Record<string, string>;
if (icons) {
  icons["video"] =
    '<svg viewBox="0 0 18 18"><rect x="1" y="3" width="16" height="12" rx="1" fill="currentColor" opacity=".2"/><polygon points="7 6 7 12 13 9" fill="currentColor"/></svg>';
  icons["table"] =
    '<svg viewBox="0 0 18 18"><rect x="1" y="1" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"/><line x1="1" y1="6" x2="17" y2="6" stroke="currentColor" stroke-width="1"/><line x1="1" y1="12" x2="17" y2="12" stroke="currentColor" stroke-width="1"/><line x1="6" y1="1" x2="6" y2="17" stroke="currentColor" stroke-width="1"/><line x1="12" y1="1" x2="12" y2="17" stroke="currentColor" stroke-width="1"/></svg>';
  icons["code"] =
    '<svg viewBox="0 0 18 18"><path d="M5 4L2 9l3 5M13 4l3 5-3 5M11 3L7 15" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>';
}
try {
  const Font = Quill.import("formats/font") as { whitelist?: string[] };
  if (Font && Array.isArray(Font.whitelist)) {
    Font.whitelist = ["Source Sans Pro", "Sans Serif", "Serif", "Monospace"];
    Quill.register(Font, true);
  }
} catch (_) {
  // opcional
}

export default ReactQuill;
