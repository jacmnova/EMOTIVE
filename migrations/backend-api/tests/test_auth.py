"""
Tests unitarios para autenticación
"""
import pytest
from fastapi import status


def test_login_success(client, normal_user):
    """Test login exitoso"""
    response = client.post(
        "/api/v1/auth/login",
        data={"username": normal_user.email, "password": "password123"}
    )
    assert response.status_code == status.HTTP_200_OK
    data = response.json()
    assert "access_token" in data
    assert data["token_type"] == "bearer"
    assert "user" in data
    assert data["user"]["email"] == normal_user.email


def test_login_wrong_password(client, normal_user):
    """Test login con contraseña incorrecta"""
    response = client.post(
        "/api/v1/auth/login",
        data={"username": normal_user.email, "password": "wrongpassword"}
    )
    assert response.status_code == status.HTTP_401_UNAUTHORIZED


def test_login_inactive_user(client, db):
    """Test login con usuario inactivo"""
    from app.models import User
    from app.core.security import get_password_hash
    
    user = User(
        name="Inactive",
        email="inactive@test.com",
        password=get_password_hash("password123"),
        ativo=False
    )
    db.add(user)
    db.commit()
    
    response = client.post(
        "/api/v1/auth/login",
        data={"username": user.email, "password": "password123"}
    )
    assert response.status_code == status.HTTP_403_FORBIDDEN


def test_register_success(client, db):
    """Test registro exitoso"""
    response = client.post(
        "/api/v1/auth/register",
        json={
            "name": "New User",
            "email": "newuser@test.com",
            "password": "password123",
            "usuario": True
        }
    )
    assert response.status_code == status.HTTP_200_OK
    data = response.json()
    assert data["email"] == "newuser@test.com"
    assert data["name"] == "New User"


def test_register_duplicate_email(client, normal_user):
    """Test registro con email duplicado"""
    response = client.post(
        "/api/v1/auth/register",
        json={
            "name": "Another User",
            "email": normal_user.email,
            "password": "password123"
        }
    )
    assert response.status_code == status.HTTP_400_BAD_REQUEST


def test_me_endpoint(client, admin_token):
    """Test obtener usuario actual"""
    response = client.get(
        "/api/v1/auth/me",
        headers={"Authorization": f"Bearer {admin_token}"}
    )
    assert response.status_code == status.HTTP_200_OK
    data = response.json()
    assert data["email"] == "admin@test.com"
    assert data["admin"] is True


def test_me_without_token(client):
    """Test obtener usuario sin token"""
    response = client.get("/api/v1/auth/me")
    assert response.status_code == status.HTTP_401_UNAUTHORIZED
