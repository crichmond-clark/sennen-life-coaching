import Image from 'next/image'
import Link from 'next/link'
import { getServices } from '@/sanity/fetch'

export const revalidate = 60

export default async function ServicesPage() {
  const services = await getServices()

  return (
    <div className="w-full relative flex flex-col flex-grow">
      {/* Intro Section */}
      <section className="bg-surface-container py-section-gap px-container-padding relative overflow-hidden -mt-[72px] pt-[150px]">
        {/* Background Texture */}
        <div className="absolute inset-0 bg-texture opacity-50 mix-blend-multiply"></div>
        <div className="max-w-4xl mx-auto text-center relative z-10 space-y-6">
          <span className="text-label-caps text-secondary tracking-widest uppercase">Offerings</span>
          <h1 className="text-display-xl text-primary leading-tight">Nourish Your Spirit</h1>
          <p className="text-body-lg text-on-surface-variant font-light mx-auto max-w-2xl">
            Whether you need a gentle reset or a deep transformative journey, these offerings hold the space for your unwinding. Choose the path that calls to your current season.
          </p>
        </div>
      </section>

      {/* Philosophy Brief */}
      <section className="max-w-7xl mx-auto px-container-padding py-section-gap w-full">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
          <div className="aspect-square relative organic-shape-3 overflow-hidden shadow-lg group">
            <Image 
              src="/images/tea-ritual.jpg"
              alt="Hands holding tea bowl in peaceful ritual"
              fill
              className="object-cover scale-105 group-hover:scale-100 transition-transform duration-[2000ms] ease-out"
            />
          </div>
          <div className="space-y-6">
            <h2 className="text-headline-md text-primary">Not fixing, just remembering.</h2>
            <p className="text-body-md text-on-surface-variant font-light leading-relaxed">
              My approach does not assume you are broken. Instead, these sessions are designed to help you peel back the layers of conditioning, stress, and noise to remember the wholeness that already resides within you.
            </p>
            <p className="text-body-md text-on-surface-variant font-light leading-relaxed">
              We move slowly, respecting the pace of your nervous system. Every offering is an invitation, never a demand.
            </p>
          </div>
        </div>
      </section>

      {/* Packages Section */}
      <section className="bg-surface py-section-gap px-container-padding">
        <div className="max-w-7xl mx-auto w-full grid grid-cols-1 md:grid-cols-3 gap-8">
          {services.length > 0 ? services.map((service) => (
            <div key={service._id} className="bg-surface-container-lowest rounded-3xl p-10 shadow-sm border border-outline-variant/30 hover:shadow-md transition-all duration-300 flex flex-col hover:-translate-y-2 relative group overflow-hidden">
              <div className="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-transparent via-primary-fixed to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
              
              <h3 className="text-headline-md text-primary mb-2 relative z-10">{service.title}</h3>
              <div className="flex items-baseline gap-2 mb-6 relative z-10">
                <span className="text-headline-md text-secondary">{service.price}</span>
                <span className="text-label-caps text-on-surface-variant tracking-widest uppercase">/ {service.duration}</span>
              </div>
              <p className="text-body-md text-on-surface font-light mb-8 relative z-10 flex-grow">
                {service.description}
              </p>
              
              {/* Divider */}
              <div className="h-px w-full bg-outline-variant/50 mb-8"></div>
              
              <ul className="space-y-4 mb-10 relative z-10 flex-grow">
                {service.features?.map((feature, fIndex) => (
                  <li key={fIndex} className="flex gap-3 text-body-md text-on-surface-variant font-light">
                    <span className="text-primary">•</span>
                    <span>{feature}</span>
                  </li>
                ))}
              </ul>
              
              <Link
                href="/booking"
                className="w-full py-4 text-center border-2 border-primary text-primary text-label-caps uppercase tracking-widest rounded-full hover:bg-primary hover:text-on-primary transition-colors duration-300 relative z-10 block"
              >
                Inquire Now
              </Link>
            </div>
          )) : (
            /* Fallback packages if no services from Sanity */
            [
              {
                title: 'The Grounding Session',
                price: '$120',
                duration: '90 Mins',
                description: 'A foundational experience designed to anchor your energy and calm the nervous system.',
                features: ['Guided somatic meditation', 'Breathwork for anxiety release', 'Personalized intention setting', 'Follow-up soulwork prompt'],
              },
              {
                title: 'The Align & Awaken',
                price: '$250',
                duration: 'Half Day',
                description: 'An immersive deep dive into your current energetic blocks and spiritual path.',
                features: ['Cacao heart-opening ritual', 'In-depth intuitive reading', 'Energy clearing & somatic gentle movement', 'Customized home practice plan'],
              },
              {
                title: 'The Sacred Container',
                price: '$800',
                duration: '4 Weeks',
                description: 'A transformative one-on-one mentorship for those seeking profound shifts.',
                features: ['Weekly 90-minute sessions', 'Direct voice-note support between calls', 'Bespoke guided meditations', 'Access to the Soul Community circle'],
              },
            ].map((pkg) => (
              <div key={pkg.title} className="bg-surface-container-lowest rounded-3xl p-10 shadow-sm border border-outline-variant/30 hover:shadow-md transition-all duration-300 flex flex-col hover:-translate-y-2 relative group overflow-hidden">
                <div className="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-transparent via-primary-fixed to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <h3 className="text-headline-md text-primary mb-2 relative z-10">{pkg.title}</h3>
                <div className="flex items-baseline gap-2 mb-6 relative z-10">
                  <span className="text-headline-md text-secondary">{pkg.price}</span>
                  <span className="text-label-caps text-on-surface-variant tracking-widest uppercase">/ {pkg.duration}</span>
                </div>
                <p className="text-body-md text-on-surface font-light mb-8 relative z-10 flex-grow">
                  {pkg.description}
                </p>
                <div className="h-px w-full bg-outline-variant/50 mb-8"></div>
                <ul className="space-y-4 mb-10 relative z-10 flex-grow">
                  {pkg.features.map((feature, fIndex) => (
                    <li key={fIndex} className="flex gap-3 text-body-md text-on-surface-variant font-light">
                      <span className="text-primary">•</span>
                      <span>{feature}</span>
                    </li>
                  ))}
                </ul>
                <Link href="/booking" className="w-full py-4 text-center border-2 border-primary text-primary text-label-caps uppercase tracking-widest rounded-full hover:bg-primary hover:text-on-primary transition-colors duration-300 relative z-10 block">
                  Inquire Now
                </Link>
              </div>
            ))
          )}
        </div>
      </section>

      {/* Custom Inquiry CTA */}
      <section className="max-w-3xl mx-auto text-center px-container-padding py-section-gap w-full space-y-6">
        <h2 className="text-headline-md text-primary">Need something bespoke?</h2>
        <p className="text-body-lg text-on-surface-variant font-light">
          I occasionally take on bespoke retreats or group facilitation. If you have a specific vision, let&apos;s explore it together.
        </p>
        <Link
          href="/booking"
          className="inline-block mt-4 text-secondary text-label-caps uppercase tracking-widest border-b border-secondary pb-1 hover:text-primary hover:border-primary transition-colors"
        >
          Send an Inquiry
        </Link>
      </section>
    </div>
  )
}