"""
Configuración global de pytest
"""
import pytest
from fastapi.testclient import TestClient
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from sqlalchemy.pool import StaticPool

from app.database import Base, get_db
from app.main import app
from app.models import User
from app.core.security import get_password_hash

# Base de datos en memoria para tests
SQLALCHEMY_DATABASE_URL = "sqlite:///:memory:"
engine = create_engine(
    SQLALCHEMY_DATABASE_URL,
    connect_args={"check_same_thread": False},
    poolclass=StaticPool,
)
TestingSessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)


@pytest.fixture(scope="function")
def db():
    """Crea una base de datos limpia para cada test"""
    Base.metadata.create_all(bind=engine)
    db_session = TestingSessionLocal()
    try:
        yield db_session
    finally:
        db_session.close()
        Base.metadata.drop_all(bind=engine)


@pytest.fixture(scope="function")
def client(db):
    """Cliente de prueba FastAPI con base de datos mock"""
    def override_get_db():
        try:
            yield db
        finally:
            pass
    
    app.dependency_overrides[get_db] = override_get_db
    with TestClient(app) as test_client:
        yield test_client
    app.dependency_overrides.clear()


@pytest.fixture
def admin_user(db):
    """Usuario admin para tests"""
    user = User(
        name="Admin Test",
        email="admin@test.com",
        password=get_password_hash("password123"),
        admin=True,
        ativo=True
    )
    db.add(user)
    db.commit()
    db.refresh(user)
    return user


@pytest.fixture
def normal_user(db):
    """Usuario normal para tests"""
    user = User(
        name="User Test",
        email="user@test.com",
        password=get_password_hash("password123"),
        usuario=True,
        ativo=True
    )
    db.add(user)
    db.commit()
    db.refresh(user)
    return user


@pytest.fixture
def admin_token(client, admin_user):
    """Token JWT para usuario admin"""
    response = client.post(
        "/api/v1/auth/login",
        data={"username": admin_user.email, "password": "password123"}
    )
    return response.json()["access_token"]


@pytest.fixture
def user_token(client, normal_user):
    """Token JWT para usuario normal"""
    response = client.post(
        "/api/v1/auth/login",
        data={"username": normal_user.email, "password": "password123"}
    )
    return response.json()["access_token"]
