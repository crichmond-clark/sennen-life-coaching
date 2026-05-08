import Image from 'next/image'
import { Flower, Droplet, Leaf, Sun, Heart } from 'lucide-react'
import { getAbout } from '@/sanity/fetch'
import { urlFor } from '@/sanity/image'

export const revalidate = 60

const iconMap: Record<string, React.FC<{ className?: string; strokeWidth?: number }>> = {
  Flower,
  Droplet,
  Leaf,
  Sun,
  Heart,
}

export default async function AboutPage() {
  const about = await getAbout()

  return (
    <div className="w-full relative flex flex-col flex-grow">
      {/* Hero Section */}
      <section className="relative w-full min-h-[85vh] flex items-center justify-center px-container-padding mb-section-gap -mt-[72px]">
        <div className="absolute inset-0 z-0">
          <Image 
            src={about?.heroImage ? urlFor(about.heroImage).width(1920).height(1080).url() : '/images/about-hero.jpg'}
            alt="Bali sunrise over mountains"
            fill
            className="object-cover opacity-80"
            priority
          />
          <div className="absolute inset-0 bg-gradient-to-t from-background via-background/40 to-transparent"></div>
        </div>
        <div className="relative z-10 max-w-4xl mx-auto text-center mt-20">
          <span className="text-label-caps text-secondary mb-4 block tracking-widest uppercase">The Guide</span>
          <h1 className="text-display-xl text-primary mb-6">
            {about?.heroHeading || 'Rooted in Grace, Wandering in Spirit'}
          </h1>
          <p className="text-body-lg text-on-surface-variant max-w-2xl mx-auto font-light leading-relaxed">
            {about?.heroSubtitle || 'My journey began not with a destination, but with a profound desire to listen. Between the ancient temples of Thailand and the healing waters of Bali, I found the quiet voice that guides my practice today.'}
          </p>
        </div>
      </section>

      {/* Journey Sections */}
      {about?.journeySections?.map((section, index) => (
        <section key={index} className="max-w-7xl mx-auto px-container-padding mb-section-gap w-full">
          <div className={`grid grid-cols-1 md:grid-cols-12 gap-gutter items-center ${index % 2 === 1 ? 'md:flex-row-reverse' : ''}`}>
            <div className={`relative group ${index % 2 === 1 ? 'md:col-start-8 md:col-span-5' : 'md:col-span-5 md:col-start-2'}`}>
              <div className="aspect-[3/4] relative organic-shape-1 overflow-hidden shadow-lg border-4 border-surface-container-highest">
                <Image 
                  src={section.image ? urlFor(section.image).width(800).height(1067).url() : '/images/journey.jpg'}
                  alt={section.heading}
                  fill
                  className="object-cover group-hover:scale-105 transition-transform duration-[1200ms]"
                />
              </div>
              <div className="absolute -z-10 -bottom-10 -left-10 w-64 h-64 bg-surface-container-high rounded-full opacity-50 blur-2xl"></div>
            </div>
            <div className={`space-y-8 mt-12 md:mt-0 ${index % 2 === 1 ? 'md:col-span-5 md:col-start-2' : 'md:col-span-5 md:col-start-8'}`}>
              <h2 className="text-headline-lg text-primary leading-tight">{section.heading}</h2>
              <p className="text-body-md text-on-surface-variant font-light leading-relaxed whitespace-pre-line">{section.body}</p>
            </div>
          </div>
        </section>
      ))}

      {/* Fallback Journey Section if no data */}
      {!about?.journeySections?.length && (
        <section className="max-w-7xl mx-auto px-container-padding mb-section-gap w-full">
          <div className="grid grid-cols-1 md:grid-cols-12 gap-gutter items-center">
            <div className="md:col-span-5 md:col-start-2 relative group">
              <div className="aspect-[3/4] relative organic-shape-1 overflow-hidden shadow-lg border-4 border-surface-container-highest">
                <Image 
                  src="/images/journey.jpg"
                  alt="Meditation in nature"
                  fill
                  className="object-cover group-hover:scale-105 transition-transform duration-[1200ms]"
                />
              </div>
              <div className="absolute -z-10 -bottom-10 -left-10 w-64 h-64 bg-surface-container-high rounded-full opacity-50 blur-2xl"></div>
            </div>
            <div className="md:col-span-5 md:col-start-8 space-y-8 mt-12 md:mt-0">
              <h2 className="text-headline-lg text-primary leading-tight">The Awakening in Chiang Mai</h2>
              <p className="text-body-md text-on-surface-variant font-light leading-relaxed">
                Years spent in the northern mountains of Thailand taught me the art of stillness. It wasn&apos;t about escaping reality, but rather plunging deeply into the present moment. Here, among the whispering bamboos and mindful monks, the foundation of my holistic approach was born.
              </p>
              <p className="text-body-md text-on-surface-variant font-light leading-relaxed">
                The transition from a fast-paced corporate life to intentional living was neither quick nor easy, but it was necessary. The friction of that transformation is what allows me to hold space for others navigating their own shifts.
              </p>
            </div>
          </div>
        </section>
      )}

      {/* Manifesto / Bento Grid */}
      <section className="bg-surface-container-low py-section-gap px-container-padding mb-section-gap relative">
        <div className="max-w-7xl mx-auto w-full">
          <div className="text-center mb-16 space-y-4">
            <h2 className="text-headline-lg text-primary">My Philosophy</h2>
            <p className="text-body-lg text-on-surface-variant font-light">Guiding principles for a soulful existence.</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
            {(about?.philosophyCards?.length ? about.philosophyCards : [
              { title: 'Radical Presence', body: 'Healing cannot happen in the past or the future. We cultivate practices that anchor you firmly in the tactile, breathing reality of the now.', iconName: 'Flower' },
              { title: 'Fluid Resilience', body: 'Like water carving through stone, true strength is found in adaptability. We learn to flow around obstacles rather than breaking against them.', iconName: 'Droplet' },
              { title: 'Earth Connection', body: "We are not separate from nature; we are expressions of it. Reconnecting with the earth's rhythms naturally realigns our own internal pacing.", iconName: 'Leaf' },
            ]).map((card, index) => {
              const Icon = iconMap[card.iconName] || Flower
              return (
                <div
                  key={index}
                  className={`bg-surface-container-lowest p-8 rounded-2xl shadow-sm border border-outline-variant/30 hover:shadow-md transition-shadow duration-300 relative overflow-hidden group ${
                    index === 1 ? 'md:-translate-y-8' : ''
                  }`}
                >
                  <div className="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <Icon className="w-16 h-16 text-primary" strokeWidth={1} />
                  </div>
                  <h3 className="text-headline-md text-secondary mb-4">{card.title}</h3>
                  <p className="text-body-md text-on-surface font-light leading-relaxed relative z-10">
                    {card.body}
                  </p>
                </div>
              )
            })}
          </div>
        </div>
      </section>

      {/* Gallery */}
      <section className="max-w-7xl mx-auto px-container-padding mb-section-gap w-full">
        <h2 className="text-headline-md text-primary text-center mb-16">Fragments of the Journey</h2>
        <div className="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
          <div className="md:col-span-7">
            <div className="aspect-[4/3] organic-shape-2 overflow-hidden shadow-md group">
              <Image 
                src="/images/yoga-shala.jpg"
                alt="Peaceful yoga shala in nature"
                fill
                className="object-cover scale-105 group-hover:scale-100 transition-transform duration-[2000ms] ease-out"
              />
            </div>
          </div>
          <div className="md:col-span-5 flex flex-col gap-8">
            <div className="aspect-[3/2] organic-shape-3 overflow-hidden shadow-md w-4/5 ml-auto group">
              <Image 
                src="/images/ceramics.jpg"
                alt="Ceramics and incense"
                fill
                className="object-cover scale-105 group-hover:scale-100 transition-transform duration-[2000ms] ease-out"
              />
            </div>
            <div className="aspect-square rounded-full overflow-hidden shadow-md w-3/5 mr-auto border-8 border-surface-container relative group">
              <Image 
                src="/images/meditation.jpg"
                alt="Connecting with nature"
                fill
                className="object-cover scale-105 group-hover:scale-100 transition-transform duration-[2000ms] ease-out"
              />
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}