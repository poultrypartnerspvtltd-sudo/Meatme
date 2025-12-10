import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import '../styles/globals.css'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'MeatMe - Fresh Chicken eCommerce',
  description: 'Experience the finest quality chicken meat, hygienically processed and delivered fresh to your doorstep. Farm-to-table freshness guaranteed.',
  keywords: 'chicken, fresh meat, poultry, eCommerce, Nepal, delivery',
  authors: [{ name: 'MeatMe Team' }],
  viewport: 'width=device-width, initial-scale=1',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body className={inter.className}>
        <div id="root">
          {children}
        </div>
      </body>
    </html>
  )
}
