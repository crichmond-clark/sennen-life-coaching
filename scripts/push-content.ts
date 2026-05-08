import { createClient } from '@sanity/client'
import { readFileSync } from 'fs'
import { resolve } from 'path'

const projectId = process.env.NEXT_PUBLIC_SANITY_PROJECT_ID
const dataset = process.env.NEXT_PUBLIC_SANITY_DATASET || 'production'
const apiVersion = process.env.NEXT_PUBLIC_SANITY_API_VERSION || '2025-05-08'
const token = process.env.SANITY_API_TOKEN

if (!projectId) {
  console.error('Missing NEXT_PUBLIC_SANITY_PROJECT_ID')
  process.exit(1)
}

if (!token) {
  console.error('Missing SANITY_API_TOKEN — get one at https://www.sanity.io/manage/personal/project/' + projectId + '/api#tokens')
  process.exit(1)
}

const client = createClient({
  projectId,
  dataset,
  apiVersion,
  token,
  useCdn: false,
})

async function pushSingleton(filePath: string, docId: string) {
  const fullPath = resolve(process.cwd(), filePath)
  const doc = JSON.parse(readFileSync(fullPath, 'utf-8'))
  doc._id = docId

  console.log(`Pushing ${doc._type}...`)
  await client.createOrReplace(doc)
  console.log(`  ✓ ${doc._type} updated`)
}

async function pushDocuments(filePath: string) {
  const fullPath = resolve(process.cwd(), filePath)
  const docs = JSON.parse(readFileSync(fullPath, 'utf-8'))

  for (const doc of docs) {
    console.log(`Pushing ${doc._type}: ${doc.title || doc.authorName || 'document'}...`)
    await client.createOrReplace(doc)
    console.log(`  ✓ ${doc._type} created/replaced`)
  }
}

async function main() {
  console.log(`Pushing content to ${projectId}/${dataset}...\n`)

  await pushSingleton('content/site-settings.json', 'siteSettings')
  await pushSingleton('content/home.json', 'home')
  await pushSingleton('content/about.json', 'about')

  await pushDocuments('content/services.json')
  await pushDocuments('content/testimonials.json')

  console.log('\n✅ All content pushed successfully.')
  console.log('   Changes will appear on the site within 60 seconds (ISR revalidation).')
}

main().catch((err) => {
  console.error('\n❌ Push failed:', err.message)
  process.exit(1)
})
