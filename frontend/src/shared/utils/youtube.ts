export function getYoutubeVideoId(url: string) {
  if (!url.trim()) return ''

  try {
    const parsed = new URL(url)
    const host = parsed.hostname.replace(/^www\./, '')

    if (host === 'youtu.be') {
      return parsed.pathname.split('/').filter(Boolean)[0] ?? ''
    }

    if (['youtube.com', 'm.youtube.com', 'music.youtube.com'].includes(host)) {
      if (parsed.pathname.startsWith('/watch')) return parsed.searchParams.get('v') ?? ''
      if (parsed.pathname.startsWith('/embed/') || parsed.pathname.startsWith('/shorts/')) {
        return parsed.pathname.split('/').filter(Boolean)[1] ?? ''
      }
    }
  } catch {
    return ''
  }

  return ''
}

export function getYoutubeThumbnailUrl(url: string) {
  const videoId = getYoutubeVideoId(url)
  return videoId ? `https://img.youtube.com/vi/${videoId}/hqdefault.jpg` : ''
}
