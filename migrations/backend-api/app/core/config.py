"""
Configuración de la aplicación
"""
from pydantic_settings import BaseSettings
from pydantic import field_validator
from typing import List, Union

def _normalize_database_url(url: str) -> str:
    """Railway y otros proveedores pueden dar postgres://; SQLAlchemy usa postgresql://."""
    if url and url.startswith("postgres://"):
        return "postgresql://" + url[len("postgres://"):]
    return url or ""

class Settings(BaseSettings):
    # App
    APP_NAME: str = "E.MO.TI.VE API"
    APP_ENV: str = "local"
    DEBUG: bool = True
    
    # Database (Railway inyecta DATABASE_URL al conectar PostgreSQL)
    DATABASE_URL: str = "sqlite:///./database.sqlite"
    # Para MySQL: "mysql+pymysql://user:password@localhost/dbname"
    # Para PostgreSQL (Railway): se usa la variable DATABASE_URL del servicio
    
    # Security (SECRET_KEY: sin comillas en .env; si la cambias, todos los tokens dejan de valer)
    SECRET_KEY: str = "ybase64:MnuI6Uf486MlCxfxPJNbD2AfCr6kCcmt/BliO9gz+yE="
    ALGORITHM: str = "HS256"
    ACCESS_TOKEN_EXPIRE_MINUTES: int = 60 * 24  # 24 horas

    @field_validator("SECRET_KEY", mode="after")
    @classmethod
    def strip_secret_key(cls, v: str) -> str:
        return (v or "").strip()

    @field_validator("DATABASE_URL", mode="after")
    @classmethod
    def normalize_database_url(cls, v: str) -> str:
        return _normalize_database_url(v or "")

    @field_validator("CORS_ORIGINS", mode="before")
    @classmethod
    def parse_cors_origins(cls, v: Union[str, List[str]]) -> List[str]:
        if isinstance(v, list):
            return v
        if isinstance(v, str):
            return [x.strip() for x in v.split(",") if x.strip()]
        return ["http://localhost:3000", "http://127.0.0.1:3000"]
    
    # CORS (en Railway puede ser una lista JSON o string separado por comas)
    CORS_ORIGINS: List[str] = ["http://localhost:3000", "http://127.0.0.1:3000"]
    
    # OpenAI
    OPENAI_API_KEY: str = ""
    OPENAI_API_URL: str = "https://api.openai.com/v1/chat/completions"
    OPENAI_MODEL: str = "gpt-4o"
    
    # Frontend (para enlaces en emails)
    FRONTEND_URL: str = "http://localhost:3000"

    # Email (compatible con .env de Laravel/PHP: mismas variables MAIL_*)
    MAIL_MAILER: str = "smtp"
    MAIL_HOST: str = "smtp.gmail.com"
    MAIL_PORT: int = 587
    MAIL_USERNAME: str = ""
    MAIL_PASSWORD: str = ""
    MAIL_ENCRYPTION: str = "tls"  # tls | ssl | null
    MAIL_FROM_ADDRESS: str = ""
    MAIL_FROM_NAME: str = "E.MO.TI.VE"
    MAIL_FROM: str = ""  # Fallback: si no hay MAIL_FROM_ADDRESS, se usa MAIL_USERNAME o este
    MAIL_USE_TLS: bool = True  # Derivado de MAIL_ENCRYPTION si no se setea
    CONTACT_EMAIL: str = "instrumentos@fellipelli.com.br"  # Destino del formulario de contacto
    
    # PDF Service
    PDF_SERVICE_URL: str = "http://127.0.0.1:8080/convert-url"
    
    # File Storage
    UPLOAD_DIR: str = "./uploads"
    MAX_UPLOAD_SIZE: int = 10 * 1024 * 1024  # 10MB
    
    # Static files (para servir uploads)
    STATIC_URL: str = "/uploads"
    
    class Config:
        env_file = ".env"
        case_sensitive = True

settings = Settings()
