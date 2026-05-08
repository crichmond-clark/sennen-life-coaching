import type { Metadata } from 'next'
import Image from 'next/image'
import { urlFor } from '@/sanity/image'
import { getHome } from '@/sanity/fetch'

export const metadata: Metadata = {
  title: 'Testimonials',
  description: 'Hear from clients who have walked the path of transformation and healing.',
}

export const revalidate = 60

export default async function TestimonialsPage() {
  const { testimonials } = await getHome()

  return (
    <div className="w-full relative flex flex-col flex-grow">
      {/* Header Section */}
      <section className="bg-surface-container py-section-gap px-container-padding relative overflow-hidden -mt-[72px] pt-[150px]">
        <div className="absolute inset-0 bg-texture opacity-50 mix-blend-multiply"></div>
        <div className="max-w-3xl mx-auto text-center relative z-10 space-y-6">
          <span className="text-label-caps text-secondary tracking-widest uppercase block">Stories of Transformation</span>
          <h1 className="text-display-xl text-primary leading-tight">What Clients Say</h1>
          <p className="text-body-lg text-on-surface-variant font-light max-w-2xl mx-auto">
            Words from those who have walked this path with me. Each story is a testament to the power of presence, compassion, and intentional healing.
          </p>
        </div>
      </section>

      {/* Testimonials Grid */}
      <section className="max-w-7xl mx-auto px-container-padding py-section-gap w-full">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {testimonials.map((testimonial: any, index: number) => (
            <div
              key={testimonial._id}
              className="bg-surface-container-lowest p-10 rounded-3xl shadow-sm border border-outline-variant/30 relative overflow-hidden group"
            >
              {/* Decorative quote mark */}
              <div className="absolute top-6 left-6 text-[80px] text-secondary/10 font-serif leading-none">&ldquo;</div>

              {/* Quote */}
              <p className="text-body-lg text-on-surface font-light leading-relaxed mb-8 relative z-10">
                &ldquo;{testimonial.quote}&rdquo;
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
      </section>
    </div>
  )
}
