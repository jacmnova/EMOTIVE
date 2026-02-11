import { test, expect } from '@playwright/test'

test.describe('Dashboard', () => {
  test.beforeEach(async ({ page }) => {
    // Mock login - guardar token en localStorage
    await page.goto('/')
    await page.evaluate(() => {
      localStorage.setItem('access_token', 'mock-token')
      localStorage.setItem('user', JSON.stringify({
        id: 1,
        name: 'Test User',
        email: 'test@test.com',
        sa: false,
        admin: true,
        gestor: false,
        usuario: true,
        ativo: true,
        created_at: '2024-01-01',
        updated_at: '2024-01-01'
      }))
    })
  })

  test('debe mostrar dashboard después de login', async ({ page }) => {
    await page.goto('/dashboard')
    await expect(page.getByText(/bem-vindo/i)).toBeVisible({ timeout: 5000 })
  })

  test('debe mostrar navegación', async ({ page }) => {
    await page.goto('/dashboard')
    await expect(page.getByRole('link', { name: /inicio/i })).toBeVisible()
    await expect(page.getByRole('link', { name: /meus questionários/i })).toBeVisible()
  })

  test('debe mostrar links de admin si es admin', async ({ page }) => {
    await page.goto('/dashboard')
    await expect(page.getByRole('link', { name: /usuários/i })).toBeVisible()
    await expect(page.getByRole('link', { name: /clientes/i })).toBeVisible()
    await expect(page.getByRole('link', { name: /formulários/i })).toBeVisible()
  })
})
