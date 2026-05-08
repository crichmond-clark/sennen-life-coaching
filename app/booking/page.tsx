import type { Metadata } from 'next'
import { getSiteSettings, getBooking } from '@/sanity/fetch'
import { BookingEmbed } from '@/components/BookingEmbed'
import { ContactForm } from '@/components/ContactForm'

export const metadata: Metadata = {
  title: 'Book a Session',
  description: 'Schedule a coaching session or send an inquiry. In-person sessions in Ubud and virtual sessions via Zoom available.',
}

export const revalidate = 60

export default async function BookingPage() {
  const settings = await getSiteSettings()
  const booking = await getBooking()

  const faqs = booking?.faqs?.length ? booking.faqs : [
    {
      question: 'Where do sessions take place?',
      answer: 'In-person sessions are held at my private shala in Ubud. Virtual sessions take place via Zoom.',
    },
    {
      question: 'What is your cancellation policy?',
      answer: 'I ask for 48 hours notice for a full refund, honoring both your time and mine.',
    },
  ]

  return (
    <div className="w-full relative flex flex-col flex-grow">
      {/* Header Section */}
      <section className="bg-surface-container py-section-gap px-container-padding relative overflow-hidden -mt-[72px] pt-[150px]">
        {/* Background Texture */}
        <div className="absolute inset-0 bg-texture opacity-50 mix-blend-multiply"></div>
        <div className="absolute inset-0 bg-gradient-to-b from-transparent to-background/80"></div>
        <div className="max-w-3xl mx-auto text-center relative z-10 space-y-6">
          <h1 className="text-display-xl text-primary leading-tight">
            {booking?.heroHeading || 'Begin Your Journey'}
          </h1>
          <p className="text-body-lg text-on-surface-variant font-light">
            {booking?.heroSubtitle || 'Take a deep breath. Inquire about a session below, or simply send a note to connect. I look forward to holding space for you.'}
          </p>
        </div>
      </section>

      <section className="max-w-7xl mx-auto px-container-padding py-16 w-full -mt-20 relative z-20">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

          {/* Booking Embed Column */}
          <div className="bg-surface-container-lowest p-8 md:p-12 rounded-3xl shadow-lg border border-outline-variant/30">
            <h2 className="text-headline-md text-primary mb-8 border-b border-outline-variant/30 pb-4">
              {booking?.scheduleHeading || 'Schedule Your Session'}
            </h2>
            <BookingEmbed bookingUrl={settings?.bookingUrl} />
          </div>

          {/* Contact Form Column */}
          <div className="space-y-12">
            <div>
              <h2 className="text-headline-md text-primary mb-6">
                {booking?.contactHeading || 'Or send a gentle note'}
              </h2>
              <ContactForm contactEmail={settings?.contactEmail} />
            </div>

            {/* Micro FAQ */}
            <div className="space-y-6 pt-8 border-t border-outline-variant/30">
              <h3 className="text-headline-md text-primary">
                {booking?.faqHeading || 'Good to know'}
              </h3>
              <div className="space-y-4">
                {faqs.map((faq, i) => (
                  <div key={i}>
                    <h4 className="text-body-md font-bold text-on-surface mb-1">{faq.question}</h4>
                    <p className="text-body-md text-on-surface-variant font-light">{faq.answer}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>

        </div>
      </section>
    </div>
  )
}