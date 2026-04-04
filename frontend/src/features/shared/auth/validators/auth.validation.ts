import { z } from 'zod'
import { toTypedSchema } from '@vee-validate/zod'

export const loginSchema = toTypedSchema(
  z.object({
    email: z
      .string()
      .min(1, 'Email là bắt buộc')
      .email('Email không hợp lệ'),

    password: z
      .string()
      .min(1, 'Mật khẩu là bắt buộc')
      .min(8, 'Mật khẩu tối thiểu 8 ký tự'),

    remember: z.boolean().optional(),
  })
)

export const registerSchema = toTypedSchema(
  z.object({
    name: z
      .string()
      .min(1, 'Họ tên là bắt buộc')
      .min(2, 'Họ tên tối thiểu 2 ký tự')
      .max(50, 'Họ tên tối đa 50 ký tự'),

    email: z
      .string()
      .min(1, 'Email là bắt buộc')
      .email('Email không hợp lệ'),

    password: z
      .string()
      .min(1, 'Mật khẩu là bắt buộc')
      .min(8, 'Mật khẩu tối thiểu 8 ký tự')
      .regex(/[A-Z]/, 'Mật khẩu phải có ít nhất 1 chữ hoa')
      .regex(/[0-9]/, 'Mật khẩu phải có ít nhất 1 số'),

    password_confirmation: z
      .string()
      .min(1, 'Xác nhận mật khẩu là bắt buộc'),
  }).refine(
    (data) => data.password === data.password_confirmation,
    {
      message: 'Mật khẩu xác nhận không khớp',
      path: ['password_confirmation'],
    }
  )
)

export type LoginSchema = z.infer<typeof loginSchema>
export type RegisterSchema = z.infer<typeof registerSchema>