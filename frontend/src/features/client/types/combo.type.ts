export interface PublicCombo {
  id: string
  name: string
  description?: string | null
  price: string | number
  status: 'active'
  image?: string | null
  cinema_id: string
  cinema?: {
    id: string
    name: string
    address?: string
  }
  created_at?: string
  updated_at?: string
}
