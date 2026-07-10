/**
 * Formats a string of digits (possibly with a leading US country code) into
 * `+1 (XXX) XXX-XXXX`, building up progressively as digits are typed.
 */
export function formatUsPhone(value) {
  const digits = value
    .replace(/\D/g, '')
    .replace(/^1/, '')
    .slice(0, 10)

  if (digits.length === 0) return ''
  if (digits.length < 4) return `+1 (${digits}`
  if (digits.length < 7) return `+1 (${digits.slice(0, 3)}) ${digits.slice(3)}`
  return `+1 (${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6, 10)}`
}
