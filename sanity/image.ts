import { createImageUrlBuilder } from '@sanity/image-url'
import { client } from './client'

const imageUrlBuilder = createImageUrlBuilder(client)

export function urlFor(source: any) {
  return imageUrlBuilder.image(source)
}