/**
 * Seed Content Instructions for Sanity Studio
 * 
 * Go to: sanity.io/manage → your project → Vision tab
 * Then run the following GROQ mutations one by one:
 */

// Run each of these blocks separately in Vision:

// 1. Site Settings
/*
*[_type == "siteSettings"][0] {
  _id,
  _type
}*/

// Then create:
/*
client.createOrReplace({
  _id: 'siteSettings',
  _type: 'siteSettings',
  brandName: 'Sennen Life Coaching',
  tagline: 'Rooted in Grace',
  bookingUrl: 'https://calendly.com/your-username',
  contactEmail: 'hello@sennenlifecoaching.com',
  socialLinks: [
    { platform: 'Instagram', url: 'https://instagram.com/yourusername' },
    { platform: 'TikTok', url: 'https://tiktok.com/@yourusername' },
    { platform: 'LinkedIn', url: 'https://linkedin.com/in/yourprofile' },
    { platform: 'Facebook', url: 'https://facebook.com/yourpage' },
    { platform: 'X', url: 'https://x.com/yourusername' },
  ],
})
*/

// For home, about, services, and testimonials - 
// easier to create them manually in Sanity Studio at /studio
// The schemas are all set up and ready to use.