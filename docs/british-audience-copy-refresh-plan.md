# British Audience Copy Refresh Plan

## Table of Contents

- [1. Problem Statement](#1-problem-statement)
- [2. Goals & Non-Goals](#2-goals-non-goals)
- [3. Proposed Architecture](#3-proposed-architecture)
- [4. Component Breakdown](#4-component-breakdown)
- [5. Data Flow](#5-data-flow)
- [6. Interface Contracts](#6-interface-contracts)
- [7. File Changes](#7-file-changes)
- [8. Implementation Phases](#8-implementation-phases)
- [9. Testing Strategy](#9-testing-strategy)
- [10. Security Implications](#10-security-implications)
- [11. Risks & Tradeoffs](#11-risks-tradeoffs)
- [12. Open Questions](#12-open-questions)

## 1. Problem Statement

The current copy is too placeholder-heavy, too intense, and too generic wellness-retreat coded. It uses language like “sacred container”, “energetic blocks”, “Bali”, “Chiang Mai”, “soulwork”, and dollar pricing, which makes the site feel less credible for a British life-coaching audience.

The site needs calm, grounded, UK-appropriate copy that sounds professional, warm, and human without overclaiming outcomes or leaning on spiritual buzzwords.

## 2. Goals & Non-Goals

### Goals

- Rewrite public copy for a primarily British audience.
- Make the tone calmer, clearer, less mystical, and less salesy.
- Replace US/international cues with UK cues:
  - British spelling: enquiry, programme, centre, recognise, personalised.
  - Pounds instead of dollars if prices stay public.
  - UK-friendly phrasing around online/in-person sessions.
- Keep claims safe and honest: no guaranteed transformation, healing, medical, or therapy-like claims.
- Keep testimonials hidden until real, approved quotes exist.
- Preserve the current Astro content structure.

### Non-Goals

- No design/layout overhaul.
- No new CMS.
- No testimonials unless supplied and approved.
- No invented personal backstory for Sennen.
- No fake credentials, locations, or client outcomes.

## 3. Proposed Architecture

This is a content-only pass using the existing Astro content collections.

Copy remains in:

```txt
src/content/pages/*.md
src/content/services/*.yaml
src/content/faqs/*.yaml
src/content/settings/site.yaml
```

Components should only change if copy length exposes layout issues. The first implementation pass should avoid component changes.

## 4. Component Breakdown

- `src/content/pages/home.md` — homepage hero, intro, CTA, SEO.
- `src/content/pages/about.md` — Sennen’s positioning and approach; remove invented travel/backstory unless confirmed.
- `src/content/pages/services.md` — service intro and bespoke enquiry copy.
- `src/content/pages/booking.md` — booking and contact wording.
- `src/content/services/*.yaml` — service names, summaries, durations, pricing, formats, features, CTAs.
- `src/content/faqs/*.yaml` — practical UK-friendly answers.
- `src/content/settings/site.yaml` — tagline/contact details once confirmed.

## 5. Data Flow

1. Content is edited in Markdown/YAML.
2. Astro content collections validate the content.
3. Pages consume content through `src/lib/content.ts`.
4. Static HTML is generated with `npm run build`.

## 6. Interface Contracts

No interface changes planned.

Existing content schemas remain unchanged:

- `pages`
- `services`
- `testimonials`
- `settings`
- `faqs`

## 7. File Changes

### Modify

- `src/content/pages/home.md` — replace hero and philosophy copy.
- `src/content/pages/about.md` — replace placeholder journey/backstory with confirmed, grounded copy.
- `src/content/pages/services.md` — simplify service positioning.
- `src/content/pages/booking.md` — make booking/contact copy practical and reassuring.
- `src/content/services/*.yaml` — rename offers and rewrite descriptions/features.
- `src/content/faqs/*.yaml` — remove Ubud/private shala reference; use real UK/online details.
- `docs/post-migration-todo.md` — mark copy refresh as an active launch dependency if needed.

### Optional Modify

- `src/content/settings/site.yaml` — update tagline if “Rooted in Grace” feels too spiritual for the target audience.

## 8. Implementation Phases

### Phase 1 — Copy Direction

- Branch: continue on `feature/astro-static-migration` or create `copy/british-audience-refresh` after the Astro branch is merged.
- Commit: `docs(copy): add British audience copy plan`
- Decide:
  - How spiritual should the tone be?
  - Does Sennen want to be positioned as life coaching, wellbeing coaching, confidence coaching, career/life transitions, or something else?
  - Are sessions online only, UK in-person, or both?
  - Are prices public?

### Phase 2 — First Rewrite Pass

- Commit: `copy(site): rewrite core pages for UK audience`
- Rewrite:
  - Home
  - About
  - Services intro
  - Booking intro
- Keep copy plain, specific, and credible.

### Phase 3 — Services and Practical Details

- Commit: `copy(services): simplify coaching offers`
- Rewrite service YAML files.
- Replace dollar prices with pounds or hide pricing if unconfirmed.
- Replace mystical feature names with practical benefits/process notes.

### Phase 4 — QA and Launch Copy Review

- Commit: `chore(copy): update launch checklist`
- Run:
  - `npm run check`
  - `npm run build`
- Manual review:
  - page lengths
  - mobile layout
  - CTA clarity
  - no fake claims
  - no testimonials linked/generated

## 9. Testing Strategy

- `npm run check` validates content schemas and TypeScript.
- `npm run build` confirms static generation.
- Manual review in browser for:
  - tone
  - line wrapping
  - mobile readability
  - CTA links
  - British spelling and phrasing

## 10. Security Implications

No direct security impact. This is a content pass.

Main safety concern is claims risk: avoid implying therapy, medical treatment, guaranteed outcomes, or regulated services unless Sennen has the relevant qualifications and wants that positioning.

## 11. Risks & Tradeoffs

- If copy becomes too neutral, it may lose warmth and personality.
- If copy remains too spiritual, it may alienate a broader British audience.
- If pricing/session details are guessed, the site may need another pass later.
- If Sennen’s real niche is unclear, the copy will remain generic.

## 12. Open Questions

Resolved direction from user:

1. Sennen helps people who are struggling with depression, addiction recovery, organisation, motivation, and getting life back on track.
2. Sessions are available in person in the UK and online.
3. Pricing should be enquiry-only for now.
4. Sennen has a BSc in Psychology.
5. The tone should be less spiritual.
6. Primary CTA chosen for the first copy pass: “Make an enquiry”.

Remaining launch questions:

1. Confirm the exact UK in-person location or service area.
2. Confirm whether the site should mention depression/addiction directly on the homepage or keep that language softer.
3. Confirm contact email and form provider.
4. Confirm Calendly or alternative booking/enquiry process.
