import type { Metadata } from 'next'
import { Plus_Jakarta_Sans, Playfair_Display } from 'next/font/google'
import { getSiteSettings } from '@/sanity/fetch'
import { urlFor } from '@/sanity/image'
import { NavBar } from '@/components/NavBar'
import { Footer } from '@/components/Footer'
import './globals.css'

const jakarta = Plus_Jakarta_Sans({
  subsets: ['latin'],
  variable: '--font-jakarta',
})

const playfair = Playfair_Display({
  subsets: ['latin'],
  variable: '--font-playfair',
})

export const revalidate = 60

export async function generateMetadata(): Promise<Metadata> {
  const settings = await getSiteSettings()
  const brandName = settings?.brandName || 'Sennen Life Coaching'
  const tagline = settings?.tagline || 'Rooted in Grace'
  const description = 'A sanctuary for spiritual alignment, mindful wellness, and the slow-living philosophy. Breathe deeply, you have arrived.'
  const siteUrl = 'https://sennenlifecoaching.com'
  
  return {
    title: {
      default: `${brandName} — ${tagline}`,
      template: `%s | ${brandName}`,
    },
    description,
    metadataBase: new URL(siteUrl),
    openGraph: {
      type: 'website',
      locale: 'en_US',
      url: siteUrl,
      siteName: brandName,
      title: `${brandName} — ${tagline}`,
      description,
      images: [
        {
          url: '/images/og-image.jpg',
          width: 1200,
          height: 630,
          alt: `${brandName} — ${tagline}`,
        },
      ],
    },
    twitter: {
      card: 'summary_large_image',
      title: `${brandName} — ${tagline}`,
      description,
      images: ['/images/og-image.jpg'],
    },
    robots: {
      index: true,
      follow: true,
    },
    icons: {
      icon: '/favicon.ico',
      apple: '/apple-touch-icon.png',
    },
  }
}

export default async function RootLayout({ children }: { children: React.ReactNode }) {
  const settings = await getSiteSettings()

  return (
    <html lang="en" className={`${jakarta.variable} ${playfair.variable} scroll-smooth`}>
      <body className="bg-background text-on-background font-body-md text-body-md antialiased overflow-x-hidden selection:bg-primary-container selection:text-on-primary-container min-h-screen flex flex-col" suppressHydrationWarning>
        <NavBar
          brandName={settings?.brandName || 'Sennen Life Coaching'}
          tagline={settings?.tagline || 'Rooted in Grace'}
          logo={settings?.logo ? urlFor(settings.logo).width(200).url() : undefined}
          bookingUrl={settings?.bookingUrl}
          socialLinks={settings?.socialLinks}
        />
        <main className="flex-grow flex flex-col pt-[72px]">
          {children}
        </main>
        <Footer
          brandName={settings?.brandName || 'Sennen Life Coaching'}
          tagline={settings?.tagline || 'Rooted in Grace'}
          socialLinks={settings?.socialLinks}
        />
      </body>
    </html>
  )
}