"use client";

import { useEffect, useState } from 'react';

/**
 * Simple client component that fetches HTML from a PHP endpoint
 * and injects it into the React page. Adjust the URL as needed.
 */
export function PHPLoader() {
  const [html, setHtml] = useState<string | null>(null);

  useEffect(() => {
    // Try to fetch a small PHP-rendered fragment; change the path if needed.
    fetch('/Meatme/index.php')
      .then((res) => res.text())
      .then((text) => setHtml(text))
      .catch(() => setHtml('<p>Failed to load PHP content.</p>'));
  }, []);

  if (!html) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-24 w-24 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return <div dangerouslySetInnerHTML={{ __html: html }} />;
}
