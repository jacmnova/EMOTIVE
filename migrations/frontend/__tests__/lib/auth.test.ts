/**
 * Tests unitarios para lib/auth.ts
 */
import { login, register, setAuth, clearAuth, getStoredUser, isAuthenticated } from '@/lib/auth'

// Mock localStorage
const localStorageMock = (() => {
  let store: Record<string, string> = {}
  return {
    getItem: (key: string) => store[key] || null,
    setItem: (key: string, value: string) => { store[key] = value },
    removeItem: (key: string) => { delete store[key] },
    clear: () => { store = {} }
  }
})()

Object.defineProperty(window, 'localStorage', { value: localStorageMock })

// Mock fetch
global.fetch = jest.fn()

describe('auth', () => {
  beforeEach(() => {
    localStorageMock.clear()
    jest.clearAllMocks()
  })

  describe('setAuth', () => {
    it('debe guardar token y usuario en localStorage', () => {
      const data = {
        access_token: 'test-token',
        token_type: 'bearer',
        user: {
          id: 1,
          name: 'Test',
          email: 'test@test.com',
          sa: false,
          admin: false,
          gestor: false,
          usuario: true,
          ativo: true,
          created_at: '2024-01-01',
          updated_at: '2024-01-01'
        }
      }
      setAuth(data)
      expect(localStorage.getItem('access_token')).toBe('test-token')
      expect(localStorage.getItem('user')).toBeTruthy()
    })
  })

  describe('clearAuth', () => {
    it('debe limpiar token y usuario de localStorage', () => {
      localStorage.setItem('access_token', 'test')
      localStorage.setItem('user', '{}')
      clearAuth()
      expect(localStorage.getItem('access_token')).toBeNull()
      expect(localStorage.getItem('user')).toBeNull()
    })
  })

  describe('getStoredUser', () => {
    it('debe retornar usuario guardado', () => {
      const user = { id: 1, name: 'Test', email: 'test@test.com' }
      localStorage.setItem('user', JSON.stringify(user))
      const stored = getStoredUser()
      expect(stored).toEqual(user)
    })

    it('debe retornar null si no hay usuario', () => {
      expect(getStoredUser()).toBeNull()
    })
  })

  describe('isAuthenticated', () => {
    it('debe retornar true si hay token', () => {
      localStorage.setItem('access_token', 'test-token')
      expect(isAuthenticated()).toBe(true)
    })

    it('debe retornar false si no hay token', () => {
      expect(isAuthenticated()).toBe(false)
    })
  })

  describe('login', () => {
    it('debe hacer login exitoso', async () => {
      const mockResponse = {
        access_token: 'token',
        token_type: 'bearer',
        user: { id: 1, name: 'Test', email: 'test@test.com', sa: false, admin: false, gestor: false, usuario: true, ativo: true, created_at: '', updated_at: '' }
      }
      ;(fetch as jest.Mock).mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse
      })

      const result = await login('test@test.com', 'password')
      expect(result).toEqual(mockResponse)
      expect(localStorage.getItem('access_token')).toBe('token')
    })

    it('debe lanzar error si login falla', async () => {
      ;(fetch as jest.Mock).mockResolvedValueOnce({
        ok: false,
        json: async () => ({ detail: 'Invalid credentials' })
      })

      await expect(login('test@test.com', 'wrong')).rejects.toThrow()
    })
  })
})
