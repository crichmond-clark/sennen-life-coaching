/**
 * Seed script to populate Sanity with initial content
 * 
 * Usage: 
 *   npx sanity exec scripts/seed-sanity.ts --with-terminal
 * 
 * Or set up via Sanity Studio's Vision plugin to run GROQ mutations.
 */

import { createClient } from '@sanity/client'
import { projectId, dataset, apiVersion } from '../sanity/env'

const client = createClient({
  projectId,
  dataset,
  apiVersion,
  useCdn: false,
})

async function seed() {
  console.log('Seeding Sanity content...\n')

  // 1. Site Settings (singleton - documentId fixed)
  console.log('Creating Site Settings...')
  await client.createOrReplace({
    _id: 'siteSettings',
    _type: 'siteSettings',
    brandName: 'Sennen Life Coaching',
    tagline: 'Rooted in Grace',
    bookingUrl: 'https://calendly.com/your-calendly-username', // Replace with actual Calendly URL
    contactEmail: 'hello@sennenlifecoaching.com', // Replace with actual email
    socialLinks: [
      { platform: 'Instagram', url: 'https://instagram.com/yourusername' },
      { platform: 'TikTok', url: 'https://tiktok.com/@yourusername' },
      { platform: 'LinkedIn', url: 'https://linkedin.com/in/yourprofile' },
      { platform: 'Facebook', url: 'https://facebook.com/yourpage' },
      { platform: 'X', url: 'https://x.com/yourusername' },
    ],
  })
  console.log('✓ Site Settings created\n')

  // 2. Home Page (singleton)
  console.log('Creating Home Page content...')
  await client.createOrReplace({
    _id: 'home',
    _type: 'home',
    heroHeading: 'Rooted in Grace',
    heroSubtitle: 'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.',
    heroCtaText: 'Begin Your Journey',
    philosophyHeading: 'The Art of Slowing Down.',
    philosophyBody: [
      {
        _type: 'block',
        children: [
          {
            _type: 'span',
            text: 'In a world that constantly demands more, we offer a space to simply be. Our philosophy is rooted in the earth, drawing inspiration from the tactile textures of nature and the gentle rhythm of the tides.',
          },
        ],
      },
      {
        _type: 'block',
        children: [
          {
            _type: 'span',
            text: 'Here, every breath is intentional, every movement is unhurried. We believe true luxury is found in connection—to oneself, to the environment, and to the present moment.',
          },
        ],
      },
    ],
  })
  console.log('✓ Home Page content created\n')

  // 3. About Page (singleton)
  console.log('Creating About Page content...')
  await client.createOrReplace({
    _id: 'about',
    _type: 'about',
    heroHeading: 'Rooted in Grace, Wandering in Spirit',
    heroSubtitle: 'My journey began not with a destination, but with a profound desire to listen. Between the ancient temples of Thailand and the healing waters of Bali, I found the quiet voice that guides my practice today.',
    journeySections: [
      {
        _type: 'journeySection',
        heading: 'The Awakening in Chiang Mai',
        body: 'Years spent in the northern mountains of Thailand taught me the art of stillness. It wasn\'t about escaping reality, but rather plunging deeply into the present moment. Here, among the whispering bamboos and mindful monks, the foundation of my holistic approach was born.\n\nThe transition from a fast-paced corporate life to intentional living was neither quick nor easy, but it was necessary. The friction of that transformation is what allows me to hold space for others navigating their own shifts.',
      },
    ],
    philosophyCards: [
      {
        _type: 'philosophyCard',
        title: 'Radical Presence',
        body: 'Healing cannot happen in the past or the future. We cultivate practices that anchor you firmly in the tactile, breathing reality of the now.',
        iconName: 'Flower',
      },
      {
        _type: 'philosophyCard',
        title: 'Fluid Resilience',
        body: 'Like water carving through stone, true strength is found in adaptability. We learn to flow around obstacles rather than breaking against them.',
        iconName: 'Droplet',
      },
      {
        _type: 'philosophyCard',
        title: 'Earth Connection',
        body: 'We are not separate from nature; we are expressions of it. Reconnecting with the earth\'s rhythms naturally realigns our own internal pacing.',
        iconName: 'Leaf',
      },
    ],
  })
  console.log('✓ About Page content created\n')

  // 4. Services
  console.log('Creating Services...')
  const services = [
    {
      _type: 'service',
      title: 'The Grounding Session',
      price: '$120',
      duration: '90 Mins',
      description: 'A foundational experience designed to anchor your energy and calm the nervous system.',
      features: [
        'Guided somatic meditation',
        'Breathwork for anxiety release',
        'Personalized intention setting',
        'Follow-up soulwork prompt',
      ],
      sortOrder: 1,
    },
    {
      _type: 'service',
      title: 'The Align & Awaken',
      price: '$250',
      duration: 'Half Day',
      description: 'An immersive deep dive into your current energetic blocks and spiritual path.',
      features: [
        'Cacao heart-opening ritual',
        'In-depth intuitive reading',
        'Energy clearing & somatic gentle movement',
        'Customized home practice plan',
      ],
      sortOrder: 2,
    },
    {
      _type: 'service',
      title: 'The Sacred Container',
      price: '$800',
      duration: '4 Weeks',
      description: 'A transformative one-on-one mentorship for those seeking profound shifts.',
      features: [
        'Weekly 90-minute sessions',
        'Direct voice-note support between calls',
        'Bespoke guided meditations',
        'Access to the Soul Community circle',
      ],
      sortOrder: 3,
    },
  ]

  for (const service of services) {
    await client.create(service)
    console.log(`  ✓ Created service: ${service.title}`)
  }
  console.log('')

  // 5. Testimonials
  console.log('Creating Testimonials...')
  const testimonials = [
    {
      _type: 'testimonial',
      quote: 'Sennen has a way of holding space that feels like coming home. Her sessions helped me find a stillness I didn\'t know was possible.',
      authorName: 'Aria Chen',
      authorTitle: 'Artist, Bali',
    },
    {
      _type: 'testimonial',
      quote: 'Working with Sennen felt like being guided back to myself. She helped me see patterns I\'d been blind to for years.',
      authorName: 'James Okafor',
      authorTitle: 'Entrepreneur, London',
    },
    {
      _type: 'testimonial',
      quote: 'The Sacred Container program changed my life. I came in feeling lost and left with a clarity I didn\'t know I was seeking.',
      authorName: 'Maya Patel',
      authorTitle: 'Yoga Instructor, Chiang Mai',
    },
  ]

  for (const testimonial of testimonials) {
    await client.create(testimonial)
    console.log(`  ✓ Created testimonial: ${testimonial.authorName}`)
  }
  console.log('')

  console.log('✅ Seeding complete!')
  console.log('\nNext steps:')
  console.log('1. Go to /studio and verify all content is visible')
  console.log('2. Upload images for hero sections and services')
  console.log('3. Update bookingUrl with your actual Calendly link')
  console.log('4. Update contactEmail with your actual email')
  console.log('5. Update socialLinks with your actual profiles')
}

seed().catch(console.error)