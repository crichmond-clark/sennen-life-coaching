import { NextRequest, NextResponse } from 'next/server'
import { Resend } from 'resend'

const resend = new Resend(process.env.RESEND_API_KEY)

const inquiryTypes = ['General Question', 'Bespoke Retreat', 'Virtual Session', 'In-Person Session']

export async function POST(req: NextRequest) {
  try {
    const body = await req.json()
    const { name, email, inquiryType, message } = body

    // Validation
    if (!name || name.trim().length < 2) {
      return NextResponse.json({ error: 'Name is required (at least 2 characters)' }, { status: 400 })
    }

    if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
      return NextResponse.json({ error: 'Valid email address is required' }, { status: 400 })
    }

    if (!inquiryType || !inquiryTypes.includes(inquiryType)) {
      return NextResponse.json({ error: 'Invalid inquiry type' }, { status: 400 })
    }

    if (!message || message.trim().length < 10) {
      return NextResponse.json({ error: 'Message is required (at least 10 characters)' }, { status: 400 })
    }

    // Get recipient from env or use default
    const recipientEmail = process.env.CONTACT_EMAIL || 'hello@sennenlifecoaching.com'

    // Send email via Resend
    const { data, error } = await resend.emails.send({
      from: 'Sennen Life Coaching <onboarding@resend.dev>',
      to: [recipientEmail],
      replyTo: email,
      subject: `New inquiry from ${name} — ${inquiryType}`,
      html: `
        <h2>New Inquiry from Website</h2>
        <p><strong>Name:</strong> ${name}</p>
        <p><strong>Email:</strong> ${email}</p>
        <p><strong>Inquiry Type:</strong> ${inquiryType}</p>
        <p><strong>Message:</strong></p>
        <p>${message.replace(/\n/g, '<br>')}</p>
        <hr>
        <p><small>Sent from sennenlifecoaching.com contact form</small></p>
      `,
    })

    if (error) {
      console.error('Resend error:', error)
      return NextResponse.json({ error: 'Failed to send message. Please try again.' }, { status: 500 })
    }

    return NextResponse.json({ success: true, id: data?.id })
  } catch (err) {
    console.error('Contact form error:', err)
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 })
  }
}