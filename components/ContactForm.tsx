'use client'

import { useState } from 'react'
import { Send } from 'lucide-react'

interface ContactFormProps {
  contactEmail?: string
}

const inquiryTypes = [
  'General Question',
  'Bespoke Retreat',
  'Virtual Session',
  'In-Person Session',
]

export function ContactForm({ contactEmail }: ContactFormProps) {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    inquiryType: inquiryTypes[0],
    message: '',
  })
  const [status, setStatus] = useState<'idle' | 'submitting' | 'success' | 'error'>('idle')
  const [errors, setErrors] = useState<Record<string, string>>({})

  const validate = () => {
    const newErrors: Record<string, string> = {}
    if (!formData.name.trim() || formData.name.length < 2) {
      newErrors.name = 'Please enter your name'
    }
    if (!formData.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
      newErrors.email = 'Please enter a valid email'
    }
    if (!formData.message.trim() || formData.message.length < 10) {
      newErrors.message = 'Please enter a message (at least 10 characters)'
    }
    return newErrors
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    
    const validationErrors = validate()
    if (Object.keys(validationErrors).length > 0) {
      setErrors(validationErrors)
      return
    }

    setErrors({})
    setStatus('submitting')

    try {
      const response = await fetch('/api/contact', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      })

      if (response.ok) {
        setStatus('success')
        setFormData({ name: '', email: '', inquiryType: inquiryTypes[0], message: '' })
      } else {
        const data = await response.json()
        setErrors({ submit: data.error || 'Failed to send message. Please try again.' })
        setStatus('error')
      }
    } catch {
      setErrors({ submit: 'Something went wrong. Please try again.' })
      setStatus('error')
    }
  }

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value } = e.target
    setFormData(prev => ({ ...prev, [name]: value }))
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }))
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {status === 'success' && (
        <div className="p-4 bg-primary-container text-on-primary-container rounded-xl text-body-md">
          Your message has been sent. I look forward to connecting with you soon.
        </div>
      )}
      
      {(status === 'error' || errors.submit) && (
        <div className="p-4 bg-error-container text-on-error-container rounded-xl text-body-md">
          {errors.submit || 'Something went wrong. Please try again.'}
        </div>
      )}

      <div className="space-y-2">
        <label htmlFor="name" className="text-label-caps text-on-surface-variant uppercase tracking-widest">Name</label>
        <input
          type="text"
          id="name"
          name="name"
          value={formData.name}
          onChange={handleChange}
          className={`w-full bg-transparent border-b py-3 text-body-lg text-on-surface focus:outline-none transition-colors ${
            errors.name ? 'border-error' : 'border-outline-variant focus:border-primary'
          }`}
          placeholder="Your full name"
        />
        {errors.name && <p className="text-label-caps text-error mt-1">{errors.name}</p>}
      </div>

      <div className="space-y-2">
        <label htmlFor="email" className="text-label-caps text-on-surface-variant uppercase tracking-widest">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          value={formData.email}
          onChange={handleChange}
          className={`w-full bg-transparent border-b py-3 text-body-lg text-on-surface focus:outline-none transition-colors ${
            errors.email ? 'border-error' : 'border-outline-variant focus:border-primary'
          }`}
          placeholder="hello@example.com"
        />
        {errors.email && <p className="text-label-caps text-error mt-1">{errors.email}</p>}
      </div>

      <div className="space-y-2">
        <label htmlFor="inquiryType" className="text-label-caps text-on-surface-variant uppercase tracking-widest">Inquiry Type</label>
        <select
          id="inquiryType"
          name="inquiryType"
          value={formData.inquiryType}
          onChange={handleChange}
          className="w-full bg-transparent border-b border-outline-variant py-3 text-body-lg text-on-surface focus:outline-none focus:border-primary transition-colors appearance-none"
        >
          {inquiryTypes.map(type => (
            <option key={type} value={type}>{type}</option>
          ))}
        </select>
      </div>

      <div className="space-y-2">
        <label htmlFor="message" className="text-label-caps text-on-surface-variant uppercase tracking-widest">Message</label>
        <textarea
          id="message"
          name="message"
          rows={4}
          value={formData.message}
          onChange={handleChange}
          className={`w-full bg-transparent border-b py-3 text-body-lg text-on-surface focus:outline-none transition-colors resize-none ${
            errors.message ? 'border-error' : 'border-outline-variant focus:border-primary'
          }`}
          placeholder="Share a bit about what brings you here..."
        />
        {errors.message && <p className="text-label-caps text-error mt-1">{errors.message}</p>}
      </div>

      <button
        type="submit"
        disabled={status === 'submitting'}
        className="group flex items-center justify-center gap-3 w-full sm:w-auto px-8 py-4 bg-surface-container-highest text-primary hover:bg-secondary-container hover:text-on-secondary rounded-full text-label-caps uppercase tracking-widest transition-all duration-300 disabled:opacity-50"
      >
        <span>{status === 'submitting' ? 'Sending...' : 'Send Message'}</span>
        <Send className="w-4 h-4 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 transition-transform" />
      </button>
    </form>
  )
}