'use client'

import { useEffect, useRef } from 'react'

interface BookingEmbedProps {
  bookingUrl?: string
}

export function BookingEmbed({ bookingUrl }: BookingEmbedProps) {
  const containerRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (!bookingUrl || !containerRef.current) return

    // Check if already loaded
    if (containerRef.current.querySelector('.calendly-inline-widget')) return

    const script = document.createElement('script')
    script.src = 'https://assets.calendly.com/assets/external/widget.js'
    script.async = true
    document.body.appendChild(script)

    return () => {
      // Cleanup on unmount
      const existingScript = document.querySelector('script[src="https://assets.calendly.com/assets/external/widget.js"]')
      if (existingScript) {
        existingScript.remove()
      }
    }
  }, [bookingUrl])

  if (!bookingUrl) {
    return (
      <div className="flex flex-col items-center justify-center py-16 text-center space-y-4">
        <div className="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center">
          <svg className="w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <p className="text-body-md text-on-surface-variant font-light">
          Calendly booking widget will appear here once configured.
        </p>
        <p className="text-label-caps text-on-surface-variant tracking-widest uppercase text-sm">
          Set up your Calendly URL in Sanity Studio
        </p>
      </div>
    )
  }

  return (
    <div
      ref={containerRef}
      className="calendly-inline-widget"
      data-url={bookingUrl}
      style={{ minWidth: '320px', height: '700px' }}
    />
  )
}