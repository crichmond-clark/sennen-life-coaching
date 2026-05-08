import { client } from './client'
import {
  siteSettingsQuery,
  homeQuery,
  aboutQuery,
  servicesQuery,
} from './queries'
import type { SiteSettings, HomePage, AboutPage, Service } from './types'

export async function getSiteSettings(): Promise<SiteSettings | null> {
  return client.fetch(siteSettingsQuery)
}

export async function getHome(): Promise<{ home: HomePage | null; testimonials: any[] }> {
  return client.fetch(homeQuery)
}

export async function getAbout(): Promise<AboutPage | null> {
  return client.fetch(aboutQuery)
}

export async function getServices(): Promise<Service[]> {
  return client.fetch(servicesQuery)
}