import { format } from 'date-fns'

export function formatDate(date) {
  //convert date to Date object to string eg: January 1, 2020
  if (!date) return ''
  return format(new Date(date), 'MMMM d, yyyy')
}

export function formatDateTime(date) {
  if (!date) return ''
  return format(new Date(date), 'yyyy-MM-dd HH:mm:ss')
}

export function formatTime(date) {
  if (!date) return ''
  return format(new Date(date), 'HH:mm:ss')
}
