import { computed } from 'vue'
import { useRoute } from 'vue-router'

export interface BreadcrumbItem {
  label: string
  name?: string
}

export function useBreadcrumb() {
  const route = useRoute()

  const breadcrumbs = computed(() =>
    (route.meta.breadcrumb ?? []) as BreadcrumbItem[]
  )

  const pageTitle = computed(() => {
    const crumbs = breadcrumbs.value
    return crumbs[crumbs.length - 1]?.label ?? ''
  })

  return { breadcrumbs, pageTitle }
}