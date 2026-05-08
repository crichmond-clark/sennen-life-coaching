const sharp = require('sharp')
const fs = require('fs')
const path = require('path')

const sourceImage = path.join(__dirname, '../public/images/hero.jpg')
const appDir = path.join(__dirname, '../app')

async function generate() {
  // Icon (favicon) - 512x512, then Next.js handles sizing
  await sharp(sourceImage)
    .resize(512, 512, { fit: 'cover', position: 'center' })
    .png()
    .toFile(path.join(appDir, 'icon.png'))
  console.log('Generated app/icon.png')

  // Apple touch icon - 180x180
  await sharp(sourceImage)
    .resize(180, 180, { fit: 'cover', position: 'center' })
    .png()
    .toFile(path.join(appDir, 'apple-icon.png'))
  console.log('Generated app/apple-icon.png')

  // Open Graph image - 1200x630
  await sharp(sourceImage)
    .resize(1200, 630, { fit: 'cover', position: 'center' })
    .jpeg({ quality: 85 })
    .toFile(path.join(appDir, 'opengraph-image.jpg'))
  console.log('Generated app/opengraph-image.jpg')

  // Twitter image - 1200x630
  await sharp(sourceImage)
    .resize(1200, 630, { fit: 'cover', position: 'center' })
    .jpeg({ quality: 85 })
    .toFile(path.join(appDir, 'twitter-image.jpg'))
  console.log('Generated app/twitter-image.jpg')
}

generate().catch(console.error)
