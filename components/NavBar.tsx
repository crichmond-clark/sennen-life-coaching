'use client'

import Link from 'next/link'
import Image from 'next/image'
import { usePathname } from 'next/navigation'
import { Menu, X } from 'lucide-react'
import { useState, useEffect } from 'react'
import { motion, AnimatePresence } from 'motion/react'
import type { SocialLink } from '@/sanity/types'

interface NavBarProps {
  brandName?: string
  tagline?: string
  logo?: string
  bookingUrl?: string
  socialLinks?: SocialLink[]
}

export function NavBar({
  brandName = 'Sennen Life Coaching',
  logo,
  bookingUrl = '/booking',
  socialLinks,
}: NavBarProps) {
  const pathname = usePathname()
  const [isScrolled, setIsScrolled] = useState(false)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20)
    }
    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  const navLinks = [
    { name: 'Home', href: '/' },
    { name: 'About', href: '/about' },
    { name: 'Services', href: '/services' },
    { name: 'Booking', href: '/booking' },
  ]

  return (
    <nav className={`fixed w-full z-50 transition-all duration-300 top-0 ${isScrolled ? 'bg-background/90 backdrop-blur-md shadow-sm border-b border-outline-variant/20 py-4' : 'bg-transparent backdrop-blur-sm py-6'}`}>
      <div className="flex justify-between items-center w-full px-container-padding max-w-7xl mx-auto">
        <Link href="/" className="text-headline-md text-primary tracking-tight z-10 relative hover:opacity-80 transition-opacity">
          {logo ? (
            <Image src={logo} alt={brandName} width={160} height={40} className="h-auto w-auto" />
          ) : (
            brandName
          )}
        </Link>
        
        {/* Desktop Nav */}
        <div className="hidden md:flex items-center gap-gutter">
          {navLinks.map((link) => {
            const isActive = pathname === link.href
            return (
              <Link 
                key={link.name} 
                href={link.href}
                className={`text-label-caps tracking-widest uppercase transition-colors duration-300 ${isActive ? 'text-primary border-b-2 border-secondary font-bold' : 'text-on-surface hover:text-secondary'}`}
              >
                {link.name}
              </Link>
            )
          })}
        </div>

        <Link 
          href={bookingUrl}
          className="hidden md:inline-flex items-center justify-center px-6 py-3 bg-primary text-on-primary text-label-caps uppercase tracking-widest rounded-full shadow-[0_4px_14px_0_rgba(21,66,18,0.2)] hover:bg-surface-tint hover:-translate-y-0.5 transition-all duration-300 scale-95 active:scale-95"
        >
          Begin Your Journey
        </Link>

        {/* Mobile Menu Toggle */}
        <button className="md:hidden text-primary p-2 z-10 relative" onClick={() => setMobileMenuOpen(!mobileMenuOpen)}>
          {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
        </button>
      </div>

      {/* Mobile Menu Content */}
      <AnimatePresence>
        {mobileMenuOpen && (
          <motion.div 
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className="absolute top-full left-0 w-full bg-background/95 backdrop-blur-lg border-b border-outline-variant/20 shadow-lg px-container-padding py-8 flex flex-col gap-6 md:hidden"
          >
            {navLinks.map((link) => {
              const isActive = pathname === link.href
              return (
                <Link 
                  key={link.name} 
                  href={link.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className={`text-body-lg text-center transition-colors duration-300 ${isActive ? 'text-secondary font-bold' : 'text-on-surface'}`}
                >
                  {link.name}
                </Link>
              )
            })}
            <Link 
              href={bookingUrl}
              onClick={() => setMobileMenuOpen(false)}
              className="mt-4 flex items-center justify-center px-8 py-4 bg-primary text-on-primary text-label-caps uppercase tracking-widest rounded-full"
            >
              Begin Your Journey
            </Link>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  )
}