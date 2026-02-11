"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";

export default function NewCalculoPage() {
  const router = useRouter();
  useEffect(() => {
    router.replace("/dashboard/calculos?new=1");
  }, [router]);
  return <p className="text-gray-500">A redirecionar…</p>;
}
