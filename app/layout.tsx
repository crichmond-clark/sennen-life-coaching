import type { Metadata } from 'next'
import { Plus_Jakarta_Sans, Playfair_Display } from 'next/font/google'
import { getSiteSettings } from '@/sanity/fetch'
import { urlFor } from '@/sanity/image'
import { NavBar } from '@/components/NavBar'
import { Footer } from '@/components/Footer'
import {
  DEFAULT_DESCRIPTION,
  DEFAULT_OG_IMAGE,
  SITE_URL,
  organizationJsonLd,
  websiteJsonLd,
} from '@/lib/seo'
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
  const description = DEFAULT_DESCRIPTION
  
  return {
    title: {
      default: `${brandName} — ${tagline}`,
      template: `%s | ${brandName}`,
    },
    description,
    metadataBase: new URL(SITE_URL),
    alternates: {
      canonical: SITE_URL,
    },
    openGraph: {
      type: 'website',
      locale: 'en_US',
      url: SITE_URL,
      siteName: brandName,
      title: `${brandName} — ${tagline}`,
      description,
      images: [
        {
          url: DEFAULT_OG_IMAGE,
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
      images: [DEFAULT_OG_IMAGE],
    },
    robots: {
      index: true,
      follow: true,
    },
    icons: {
      icon: '/icon.png',
      apple: '/apple-icon.png',
    },
  }
}

export default async function RootLayout({ children }: { children: React.ReactNode }) {
  const settings = await getSiteSettings()
  const brandName = settings?.brandName || 'Sennen Life Coaching'
  const tagline = settings?.tagline || 'Rooted in Grace'
  const jsonLd = [organizationJsonLd(brandName, tagline), websiteJsonLd(brandName)]

  return (
    <html lang="en" className={`${jakarta.variable} ${playfair.variable} scroll-smooth`}>
      <body className="bg-background text-on-background text-body-md antialiased overflow-x-hidden selection:bg-primary-container selection:text-on-primary-container min-h-screen flex flex-col" suppressHydrationWarning>
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd).replace(/</g, '\\u003c') }}
        />
        <NavBar
          brandName={brandName}
          tagline={tagline}
          logo={settings?.logo ? urlFor(settings.logo).width(200).url() : undefined}
          bookingUrl={settings?.bookingUrl}
          socialLinks={settings?.socialLinks}
        />
        <main className="flex-grow flex flex-col pt-[72px]">
          {children}
        </main>
        <Footer
          brandName={brandName}
          tagline={tagline}
          socialLinks={settings?.socialLinks}
        />
      </body>
    </html>
  )
}