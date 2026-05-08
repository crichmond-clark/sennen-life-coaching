import Image from 'next/image'
import Link from 'next/link'
import { ArrowDown, ArrowRight } from 'lucide-react'
import { getHome } from '@/sanity/fetch'
import { urlFor } from '@/sanity/image'
import { Testimonials } from '@/components/Testimonials'

export const revalidate = 60

export default async function Home() {
  const { home, testimonials } = await getHome()

  return (
    <div className="relative w-full flex-grow flex flex-col">
      {/* Hero Section */}
      <section className="relative min-h-[90vh] flex items-center justify-center overflow-hidden -mt-[72px]">
        {/* Background Image */}
        <div className="absolute inset-0 z-0">
          <Image 
            src={home?.heroImage ? urlFor(home.heroImage).width(1920).height(1080).url() : '/images/hero.jpg'}
            alt="Serene tropical retreat with lush greenery and warm light"
            fill
            className="object-cover object-center"
            priority
          />
          <div className="absolute inset-0 bg-surface-container-highest/30 backdrop-blur-[2px]"></div>
          <div className="absolute inset-0 bg-gradient-to-b from-transparent via-background/20 to-background"></div>
        </div>
        
        {/* Content */}
        <div className="relative z-10 text-center px-container-padding max-w-4xl mx-auto flex flex-col items-center gap-content-gap mt-20">
          <div className="space-y-6">
            <span className="block text-label-caps text-primary tracking-[0.3em] uppercase">Find Your Center</span>
            <h1 className="text-display-xl text-primary drop-shadow-sm leading-tight">
              {home?.heroHeading || 'Rooted in Grace'}
            </h1>
            <p className="text-body-lg text-on-surface-variant max-w-2xl mx-auto font-light">
              {home?.heroSubtitle || 'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.'}
            </p>
          </div>
          <Link 
            href="/booking"
            className="inline-flex items-center justify-center px-8 py-4 bg-secondary text-on-secondary text-label-caps uppercase tracking-widest rounded-full shadow-[0_8px_24px_0_rgba(152,70,35,0.25)] hover:bg-tertiary hover:-translate-y-1 transition-all duration-300"
          >
            {home?.heroCtaText || 'Begin Your Journey'}
          </Link>
          
          {/* Scroll Indicator */}
          <div className="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-primary/70 animate-bounce">
            <span className="text-[10px] uppercase font-sans font-semibold tracking-widest">Scroll to explore</span>
            <ArrowDown className="w-5 h-5 font-light" />
          </div>
        </div>
      </section>

      {/* Botanical Divider */}
      <div className="w-full h-24 flex justify-center items-center opacity-30 my-10 relative">
        <svg fill="none" height="24" viewBox="0 0 120 24" width="120" xmlns="http://www.w3.org/2000/svg" className="text-primary">
          <path d="M10 12C30 12 40 2 60 2C80 2 90 12 110 12" stroke="currentColor" strokeLinecap="round" strokeWidth="1"></path>
          <circle cx="60" cy="2" fill="currentColor" r="2"></circle>
          <path d="M55 7C55 7 58 4 60 2C62 4 65 7 65 7" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1"></path>
        </svg>
      </div>

      {/* Philosophy Section */}
      <section className="py-section-gap px-container-padding flex-grow flex items-center">
        <div className="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-gutter items-center w-full">
          {/* Text Content */}
          <div className="md:col-span-5 md:col-start-2 space-y-8 z-10 relative">
            <h2 className="text-headline-lg text-primary">
              {home?.philosophyHeading || 'The Art of Slowing Down.'}
            </h2>
            {home?.philosophyBody ? (
              <div className="space-y-4">
                {home.philosophyBody.map((block: any, i: number) => {
                  const text = block.children?.map((child: any) => child.text).join('') || ''
                  return <p key={i} className="text-body-lg text-on-surface-variant leading-relaxed font-light">{text}</p>
                })}
              </div>
            ) : (
              <>
                <p className="text-body-lg text-on-surface-variant leading-relaxed font-light">
                  In a world that constantly demands more, we offer a space to simply be. Our philosophy is rooted in the earth, drawing inspiration from the tactile textures of nature and the gentle rhythm of the tides.
                </p>
                <p className="text-body-md text-on-surface-variant leading-relaxed opacity-90">
                  Here, every breath is intentional, every movement is unhurried. We believe true luxury is found in connection—to oneself, to the environment, and to the present moment.
                </p>
              </>
            )}
            <Link href="/about" className="inline-flex items-center gap-2 text-secondary text-label-caps uppercase tracking-widest hover:text-tertiary transition-colors group mt-4">
              <span>Our Story</span>
              <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </Link>
          </div>
          
          {/* Image Container */}
          <div className="md:col-span-5 md:col-start-8 mt-12 md:mt-0 relative group">
            <div className="aspect-[4/5] relative organic-shape-1 overflow-hidden shadow-[0_20px_40px_-15px_rgba(45,90,39,0.15)] transform transition-transform duration-700 ease-out">
              <Image 
                src={home?.philosophyImage ? urlFor(home.philosophyImage).width(800).height(1000).url() : '/images/philosophy.jpg'}
                alt="Woman in peaceful meditation in natural setting"
                fill
                className="object-cover object-center group-hover:scale-105 transition-transform duration-[1200ms]"
              />
              <div className="absolute inset-0 bg-primary/10 mix-blend-overlay"></div>
            </div>
            {/* Decorative Element */}
            <div className="absolute -bottom-8 -left-8 w-32 h-32 bg-secondary-container rounded-full mix-blend-multiply blur-2xl opacity-60"></div>
            <div className="absolute -top-8 -right-8 w-40 h-40 bg-primary-fixed rounded-full mix-blend-multiply blur-3xl opacity-40"></div>
          </div>
        </div>
      </section>

      {/* Testimonials */}
      <Testimonials testimonials={testimonials} />
    </div>
  )
}