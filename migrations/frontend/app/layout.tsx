import type { Metadata } from "next";
import { Quicksand } from "next/font/google";
import "./globals.css";

const quicksand = Quicksand({
  subsets: ["latin"],
  variable: "--font-quicksand",
  weight: ["300", "400", "500", "600", "700"],
  display: "swap",
});

export const metadata: Metadata = {
  title: "E.MO.TI.VE",
  description: "Sistema E.MO.TI.VE - Burnout | FELLIPELLI",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="pt-BR" className={quicksand.variable}>
      <body className="antialiased min-h-screen font-sans">{children}</body>
    </html>
  );
}
