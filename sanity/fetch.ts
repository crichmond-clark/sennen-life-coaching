import { client } from './client'
import {
  siteSettingsQuery,
  homeQuery,
  aboutQuery,
  bookingQuery,
  servicesQuery,
} from './queries'
import type { SiteSettings, HomePage, AboutPage, BookingPage, Service } from './types'

export async function getSiteSettings(): Promise<SiteSettings | null> {
  return client.fetch(siteSettingsQuery)
}

export async function getHome(): Promise<{ home: HomePage | null; testimonials: any[] }> {
  return client.fetch(homeQuery)
}

export async function getAbout(): Promise<AboutPage | null> {
  return client.fetch(aboutQuery)
}

export async function getBooking(): Promise<BookingPage | null> {
  return client.fetch(bookingQuery)
}

export async function getServices(): Promise<Service[]> {
  return client.fetch(servicesQuery)
}