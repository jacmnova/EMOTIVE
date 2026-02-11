"""
Configuración de base de datos SQLAlchemy
"""
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from app.core.config import settings

_connect_args = {}
if "sqlite" in settings.DATABASE_URL:
    _connect_args["check_same_thread"] = False
elif "postgresql" in settings.DATABASE_URL:
    _connect_args["connect_timeout"] = 10

engine = create_engine(
    settings.DATABASE_URL,
    connect_args=_connect_args,
    pool_pre_ping=("postgresql" in settings.DATABASE_URL),
)

SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

Base = declarative_base()

def get_db():
    """Dependency para obtener sesión de BD"""
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
