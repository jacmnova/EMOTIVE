"use client";

import { useEffect } from "react";
import { useRouter, useParams } from "next/navigation";

export default function EditCalculoPage() {
  const router = useRouter();
  const params = useParams();
  const id = params.id;
  useEffect(() => {
    if (id) router.replace(`/dashboard/calculos?edit=${id}`);
    else router.replace("/dashboard/calculos");
  }, [router, id]);
  return <p className="text-gray-500">A redirecionar…</p>;
}
