import { test, expect } from '@playwright/test'

test.describe('Autenticación', () => {
  test('debe mostrar página de login', async ({ page }) => {
    await page.goto('/auth/login')
    await expect(page.getByRole('heading', { name: /iniciar sesión/i })).toBeVisible()
    await expect(page.getByLabel(/email/i)).toBeVisible()
    await expect(page.getByLabel(/contraseña/i)).toBeVisible()
  })

  test('debe mostrar error con credenciales inválidas', async ({ page }) => {
    await page.goto('/auth/login')
    await page.getByLabel(/email/i).fill('wrong@test.com')
    await page.getByLabel(/contraseña/i).fill('wrongpassword')
    await page.getByRole('button', { name: /entrar/i }).click()
    
    // Esperar mensaje de error
    await expect(page.getByText(/incorrectos|error/i)).toBeVisible({ timeout: 5000 })
  })

  test('debe navegar a registro desde login', async ({ page }) => {
    await page.goto('/auth/login')
    await page.getByRole('link', { name: /regístrate/i }).click()
    await expect(page).toHaveURL(/.*\/auth\/register/)
  })

  test('debe mostrar formulario de registro', async ({ page }) => {
    await page.goto('/auth/register')
    await expect(page.getByRole('heading', { name: /registrarse/i })).toBeVisible()
    await expect(page.getByLabel(/nombre/i)).toBeVisible()
    await expect(page.getByLabel(/email/i)).toBeVisible()
    await expect(page.getByLabel(/contraseña/i)).toBeVisible()
  })

  test('debe mostrar página de recuperación de contraseña', async ({ page }) => {
    await page.goto('/auth/forgot-password')
    await expect(page.getByRole('heading', { name: /recuperar/i })).toBeVisible()
    await expect(page.getByLabel(/email/i)).toBeVisible()
  })
})

test.describe('Dashboard (requiere autenticación)', () => {
  test('debe redirigir a login si no está autenticado', async ({ page }) => {
    await page.goto('/dashboard')
    await expect(page).toHaveURL(/.*\/auth\/login/)
  })
})
