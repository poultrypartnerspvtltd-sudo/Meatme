import { Suspense } from 'react'
import { PHPLoader } from '../components/PHPLoader'

export default function HomePage() {
  return (
    <Suspense fallback={<div className="flex items-center justify-center min-h-screen">
      <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
    </div>}>
      <PHPLoader />
    </Suspense>
  )
}