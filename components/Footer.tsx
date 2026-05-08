import Link from 'next/link'
import type { SocialLink } from '@/sanity/types'

// Inline SVG social icons
const InstagramIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
    <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
  </svg>
)

const TikTokIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2a8.46 8.46 0 0 1 5.5 2.3c-.19-.2-.38-.4-.57-.61a2.27 2.27 0 0 1-.31-.36 4.89 4.89 0 0 1-1.05-3.04 4.89 4.89 0 0 1 1.05-3.04c.11-.14.22-.28.32-.42A8.47 8.47 0 0 1 21 5.5v.52a7.76 7.76 0 0 1-1.41 1.17z"/>
  </svg>
)

const LinkedInIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
    <rect width="4" height="12" x="2" y="9"/>
    <circle cx="4" cy="4" r="2"/>
  </svg>
)

const FacebookIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
  </svg>
)

const XIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
  </svg>
)

const iconMap: Record<string, React.FC> = {
  Instagram: InstagramIcon,
  TikTok: TikTokIcon,
  LinkedIn: LinkedInIcon,
  Facebook: FacebookIcon,
  X: XIcon,
}

interface FooterProps {
  brandName?: string
  tagline?: string
  socialLinks?: SocialLink[]
}

export function Footer({
  brandName = 'Sennen Life Coaching',
  tagline = 'Rooted in Grace',
  socialLinks,
}: FooterProps) {
  // Fallback social links if none from Sanity
  const links = socialLinks?.length
    ? socialLinks
    : [
        { platform: 'Instagram', url: '#' },
        { platform: 'TikTok', url: '#' },
        { platform: 'LinkedIn', url: '#' },
        { platform: 'Facebook', url: '#' },
        { platform: 'X', url: '#' },
      ]

  return (
    <footer className="bg-surface-container border-t border-outline-variant rounded-t-xl mt-auto relative z-10 w-full">
      <div className="flex flex-col md:flex-row justify-between items-center w-full px-container-padding py-12 gap-8 max-w-7xl mx-auto shadow-sm">
        <div className="flex flex-col items-center md:items-start gap-4 mb-4 md:mb-0">
          <span className="text-headline-md text-primary tracking-tight opacity-90 hover:opacity-100 transition-opacity">{brandName}</span>
          <span className="text-body-md text-on-surface-variant font-light">© {new Date().getFullYear()} {brandName}. {tagline}.</span>
        </div>
        
        <div className="flex items-center gap-6">
          {links.map((link) => {
            const Icon = iconMap[link.platform] || GlobeIcon
            return (
              <Link 
                key={link.platform}
                href={link.url}
                target="_blank"
                rel="noopener noreferrer"
                className="text-on-surface-variant hover:text-primary transition-colors duration-300"
                aria-label={link.platform}
              >
                <Icon />
              </Link>
            )
          })}
        </div>
      </div>
    </footer>
  )
}

// Fallback globe icon
const GlobeIcon = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="12" cy="12" r="10"/>
    <line x1="2" y1="12" x2="22" y2="12"/>
    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
  </svg>
)