'use client'

import Image from 'next/image'
import { urlFor } from '@/sanity/image'
import type { Testimonial } from '@/sanity/types'

interface TestimonialsProps {
  testimonials: Testimonial[]
}

export function Testimonials({ testimonials }: TestimonialsProps) {
  if (!testimonials?.length) return null

  return (
    <section className="py-section-gap px-container-padding bg-surface-container-low relative">
      {/* Botanical divider top */}
      <div className="w-full h-16 flex justify-center items-center opacity-30 mb-8">
        <svg fill="none" height="24" viewBox="0 0 120 24" width="120" xmlns="http://www.w3.org/2000/svg" className="text-primary">
          <path d="M10 12C30 12 40 2 60 2C80 2 90 12 110 12" stroke="currentColor" strokeLinecap="round" strokeWidth="1"></path>
          <circle cx="60" cy="2" fill="currentColor" r="2"></circle>
          <path d="M55 7C55 7 58 4 60 2C62 4 65 7 65 7" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1"></path>
        </svg>
      </div>

      <div className="max-w-7xl mx-auto w-full">
        <div className="text-center mb-16 space-y-4">
          <span className="text-label-caps text-secondary tracking-widest uppercase block">Testimonials</span>
          <h2 className="text-headline-lg text-primary">What Clients Say</h2>
          <p className="text-body-lg text-on-surface-variant font-light max-w-2xl mx-auto">
            Stories from those who have walked this path with me.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {testimonials.map((testimonial, index) => (
            <div
              key={testimonial._id}
              className={`bg-surface-container-lowest p-10 rounded-3xl shadow-sm border border-outline-variant/30 relative overflow-hidden group ${
                index === 1 ? 'md:-translate-y-4' : ''
              }`}
            >
              {/* Decorative quote mark */}
              <div className="absolute top-6 left-6 text-[80px] text-secondary/10 font-serif leading-none">"</div>
              
              {/* Quote */}
              <p className="text-body-lg text-on-surface font-light leading-relaxed mb-8 relative z-10">
                "{testimonial.quote}"
              </p>
              
              {/* Author */}
              <div className="flex items-center gap-4">
                {testimonial.avatar ? (
                  <div className="w-12 h-12 rounded-full overflow-hidden">
                    <Image
                      src={urlFor(testimonial.avatar).width(96).height(96).url()}
                      alt={testimonial.authorName}
                      width={48}
                      height={48}
                      className="object-cover w-full h-full"
                    />
                  </div>
                ) : (
                  <div className="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center">
                    <span className="text-body-md text-secondary font-medium">
                      {testimonial.authorName.charAt(0)}
                    </span>
                  </div>
                )}
                <div>
                  <p className="text-body-md font-medium text-on-surface">{testimonial.authorName}</p>
                  <p className="text-label-caps text-on-surface-variant tracking-widest uppercase">{testimonial.authorTitle}</p>
                </div>
              </div>

              {/* Top accent bar */}
              <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary/20 via-primary to-primary/20 opacity-0 group-hover:opacity-100 transition-opacity" />
            </div>
          ))}
        </div>
      </div>

      {/* Botanical divider bottom */}
      <div className="w-full h-16 flex justify-center items-center opacity-30 mt-16">
        <svg fill="none" height="24" viewBox="0 0 120 24" width="120" xmlns="http://www.w3.org/2000/svg" className="text-primary">
          <path d="M10 12C30 12 40 22 60 22C80 22 90 12 110 12" stroke="currentColor" strokeLinecap="round" strokeWidth="1"></path>
          <circle cx="60" cy="22" fill="currentColor" r="2"></circle>
          <path d="M55 17C55 17 58 20 60 22C62 20 65 17 65 17" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1"></path>
        </svg>
      </div>
    </section>
  )
}